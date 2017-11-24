<?php

class PacienteMedicacaoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "list_pacientes_medicacao" );
        $this->form->setFormTitle( "Medicação " );
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

        $column_1       = new TDataGridColumn( "nomepacientecor", "Paciente", "left" );
        $column_2       = new TDataGridColumn( "proximaaplicacao", "Proxima Aplicação", "left" );
        $column_3       = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );
        $column_4       = new TDataGridColumn( "queixaprincipal", "Queixa Principal", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );
        $this->datagrid->addColumn( $column_4 );


        $action_avaliacao = new TDatagridTablesAction( [ "MedicarPacienteDetail", "onReload" ] );
        $action_avaliacao->setButtonClass( "btn btn-primary" );
        $action_avaliacao->setImage( "fa:user-md white fa-lg" );
        $action_avaliacao->setField( "bauatendimento_id" );
        $action_avaliacao->setFk( "bau_id" );
        $action_avaliacao->setDid( "bauatendimento_id" );
        $action_avaliacao->setUseButton(TRUE);
        $this->datagrid->addQuickAction( "Medicar Paciente", $action_avaliacao, 'bauatendimento_id');

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

            $repository = new TRepository( "VwMedicacaoPacienteRecord" );

            $properties = [
                "order" => "dataentrada",
                "direction" => "desc"
            ];

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            //$criteria->add(new TFilter('status', '=', 'PRESCRITO'));

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $object->proximaaplicacao = TDate::date2br($object->proximaaplicacao) . ' ' . substr($object->proximaaplicacao, 11, strlen($object->proximaaplicacao));
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
        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );
                $repository = new TRepository( "VwMedicacaoPacienteRecord" );

                $properties = [
                    "order" => "dataentrada",
                    "direction" => "desc"
                ];

                $criteria = new TCriteria();
                $criteria->setProperties( $properties );
                //$criteria->add(new TFilter('status', '=', 'PRESCRITO'));

                switch( $data->opcao ) {

                    case "nomepaciente":

                        $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );

                        break;

                }


                $objects = $repository->load( $criteria, FALSE );

                $this->datagrid->clear();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        $dataentrada = new DateTime( $object->dataentrada );
                        $horaentrada = new DateTime( $object->horaentrada );

                        $object->dataentrada = $dataentrada->format("d/m/Y");
                        $object->horaentrada = $horaentrada->format("H:i");
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


    public function show(){

        $this->onReload();
        parent::show();

    }
}
