<?php

class PacientesEncaminhamentoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "list_pacientes_encaminhamento" );
        $this->form->setFormTitle( "Encaminhamento " );
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

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome   = new TDataGridColumn( "nomepaciente", "Paciente", "left" );
        $column_dataentrada     = new TDataGridColumn( "dataentrada", "Data de Chegada", "left" );
        $column_horaentrada     = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );
        $column_queixaprincipal = new TDataGridColumn( "queixaprincipal", "Queixa Principal", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );
        $this->datagrid->addColumn( $column_queixaprincipal );

        $action_internamento = new CustomDataGridAction( [ "EncaminhamentoDetail", "onReload" ] );
        $action_internamento->setButtonClass( "btn btn-primary" );
        $action_internamento->setImage( "fa:bed white fa-lg" );
        $action_internamento->setField( "bau_id" );
        $action_internamento->setFk( "bau_id" );
        $action_internamento->setDid( "paciente_id" );
        $action_internamento->setUseButton(TRUE);
        $this->datagrid->addQuickAction( "Internar", $action_internamento, 'bau_id');

        $action_altahospitalar = new CustomDataGridAction( [ "AltaHospitalarDetail", "onReload" ] );
        $action_altahospitalar->setButtonClass( "btn btn-default" );
        $action_altahospitalar->setImage( "fa:arrow-circle-right green fa-lg" );
        $action_altahospitalar->setField( "bau_id" );
        $action_altahospitalar->setFk( "bau_id" );
        $action_altahospitalar->setDid( "paciente_id" );
        $action_altahospitalar->setUseButton(TRUE);
        // $this->datagrid->addQuickAction( "Alta Hospitalar", $action_altahospitalar, 'bau_id');

        $action_remocao = new CustomDataGridAction( [ "EncaminhamentoDetail", "onReload" ] );
        $action_remocao->setButtonClass( "btn btn-default" );
        $action_remocao->setLabel( "Remoção" );
        $action_remocao->setImage( "fa:arrow-circle-right green fa-lg" );
        $action_remocao->setField( "bau_id" );
        $action_remocao->setFk( "bau_id" );
        $action_remocao->setDid( "paciente_id" );
        $action_remocao->setParameter( "mode", "remocao" );

        $action_transferencia = new CustomDataGridAction( [ "EncaminhamentoDetail", "onReload" ] );
        $action_transferencia->setButtonClass( "btn btn-default" );
        $action_transferencia->setLabel( "Transferência" );
        $action_transferencia->setImage( "fa:arrow-circle-right green fa-lg" );
        $action_transferencia->setField( "bau_id" );
        $action_transferencia->setFk( "bau_id" );
        $action_transferencia->setDid( "paciente_id" );
        $action_transferencia->setParameter( "mode", "transferencia" );

        $action_group = new TDataGridActionGroup('Opções', 'bs:th');
        $action_group->addAction( $action_remocao );
        $action_group->addAction( $action_transferencia );
        // $this->datagrid->addActionGroup( $action_group );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

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

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "situacao", "!=", "ALTA") );
            $criteria->add( new TFilter( "situacao", "!=", "OBITO") );
            $criteria->add( new TFilter( "situacao", "!=", "ENCAMINHADO") );


            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $dataentrada = new DateTime( $object->dataentrada );
                    $horaentrada = new DateTime( $object->horaentrada );

                    $object->dataentrada = $dataentrada->format("d/m/Y");
                    $object->horaentrada = $horaentrada->format("H:i");

                    $this->datagrid->addItem( $object );
                }

            }

            $criteria->resetProperties();

            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
            $this->pageNavigation->setLimit( $limit );

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

                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "dataentrada";
                    $param[ "direction" ] = "desc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );
                $criteria->add( new TFilter( "situacao", "!=", "ALTA") );
                $criteria->add( new TFilter( "situacao", "!=", "OBITO") );
                $criteria->add( new TFilter( "situacao", "!=", "ENCAMINHADO") );

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

                $count = $repository->count( $criteria );

                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );

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
