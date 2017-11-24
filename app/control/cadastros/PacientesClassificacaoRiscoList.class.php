<?php

class PacientesClassificacaoRiscoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "list_pacientes_classificacao_risco" );
        $this->form->setFormTitle( "Classificação de Risco" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty ( "title", "Informe os dados referentes a opção" );

        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        $opcao->addItems( [ "nomepaciente" => "Paciente" ] );

        $this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        $this->form->addAction( "Buscar Paciente", new TAction( [ $this, "onSearch" ] ), "fa:search" );

        $this->datagrid = new TDatagridTables();

        $column_paciente_nome   = new TDataGridColumn( "nomepaciente", "Nome do Paciente", "left" );
        $column_paciente_idade  = new TDataGridColumn( "idade", "Idade do Paciente", "left" );
        $column_dataentrada     = new TDataGridColumn( "dataentrada", "Data de Chegada", "left" );
        $column_horaentrada     = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_paciente_idade );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );

        $action_avaliacao = new TDatagridTablesAction( [ "ClassificacaoRiscoDetail", "onReload" ] );
        $action_avaliacao->setButtonClass( "btn btn-primary" );
        $action_avaliacao->setImage( "fa:stethoscope white fa-lg" );
        $action_avaliacao->setField( "bau_id" );
        $action_avaliacao->setFk( "bau_id" );
        $action_avaliacao->setDid( "paciente_id" );
        $action_avaliacao->setParameter( "page", __CLASS__ );
        $action_avaliacao->setUseButton(TRUE);
        $this->datagrid->addQuickAction( "Classificar Paciente", $action_avaliacao, 'bau_id');

        $this->datagrid->createModel();

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );

        parent::add( $container );
    }

    public function onReload()
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "VwBauPacientesRecord" );

            $properties = [
                "order" => "dataentrada",
                "direction" => "desc"
            ];

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->add( new TFilter( "situacao", "=", "ABERTO") );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {
                    $this->datagrid->addItem( $object );
                }

            }

            $criteria->resetProperties();

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

    public function onSearch()
    {
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );

                $repository = new TRepository( "VwBauPacientesRecord" );

                $properties = [
                    "order" => "dataentrada",
                    "direction" => "desc"
                ];

                $criteria = new TCriteria();
                $criteria->setProperties( $properties );
                $criteria->add( new TFilter( "situacao", "=", "ABERTO") );

                switch( $data->opcao ) {

                    case "nomepaciente":

                        $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );

                        break;

                }

                $objects = $repository->load( $criteria );

                $this->datagrid->clear();

                if ( $objects ) {

                    foreach ( $objects as $object ) {
                        $this->datagrid->addItem( $object );
                    }

                } else {

                  new TMessage( "info", "Não há dados cadastrados!" );

                }

                $criteria->resetProperties();

                TTransaction::close();

                $this->form->setData( $data );

                $this->loaded = true;

            } else {

                $this->onReload();

                $this->form->setData( $data );

                new TMessage( "erro", "Selecione uma opção e informe os dados à buscar corretamente!" );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $data );

            new TMessage( "erro", $ex->getMessage() );
        }
    }

    public function show()
    {
        $this->onReload();

        parent::show();
    }
}
