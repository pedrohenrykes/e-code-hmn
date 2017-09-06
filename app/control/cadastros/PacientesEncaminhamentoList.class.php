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
        $this->form->setFormTitle( "Listagem de Pacientes com Encaminhamento " );
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

        $column_paciente_nome   = new TDataGridColumn( "paciente_nome", "Paciente", "left" );
        $column_dataentrada     = new TDataGridColumn( "dataentrada", "Data de Chegada", "left" );
        $column_horaentrada     = new TDataGridColumn( "horaentrada", "Hora de Chegada", "left" );
        $column_queixaprincipal = new TDataGridColumn( "queixaprincipal", "Queixa Principal", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );
        $this->datagrid->addColumn( $column_queixaprincipal );

        $action_internamento = new CustomDataGridAction( [ "EncaminhamentoDetalhe", "onReload" ] );
        $action_internamento->setButtonClass( "btn btn-default" );
        $action_internamento->setLabel( "Encaminhar para Internamento" );
        $action_internamento->setImage( "fa:arrow-circle-right green fa-lg" );
        $action_internamento->setField( "id" );
        $action_internamento->setFk( "id" );
        $action_internamento->setDid( "paciente_id" );
        $action_internamento->setParameter( "mode", "internamento" );

        $action_remocao = new CustomDataGridAction( [ "EncaminhamentoDetalhe", "onReload" ] );
        $action_remocao->setButtonClass( "btn btn-default" );
        $action_remocao->setLabel( "Encaminhar para Remoção" );
        $action_remocao->setImage( "fa:arrow-circle-right green fa-lg" );
        $action_remocao->setField( "id" );
        $action_remocao->setFk( "id" );
        $action_remocao->setDid( "paciente_id" );
        $action_remocao->setParameter( "mode", "remocao" );

        $action_transferencia = new CustomDataGridAction( [ "EncaminhamentoDetalhe", "onReload" ] );
        $action_transferencia->setButtonClass( "btn btn-default" );
        $action_transferencia->setLabel( "Encaminhar para Transferência" );
        $action_transferencia->setImage( "fa:arrow-circle-right green fa-lg" );
        $action_transferencia->setField( "id" );
        $action_transferencia->setFk( "id" );
        $action_transferencia->setDid( "paciente_id" );
        $action_transferencia->setParameter( "mode", "transferencia" );

        $action_group = new TDataGridActionGroup('Opções', 'bs:th');
        $action_group->addAction( $action_internamento );
        $action_group->addAction( $action_remocao );
        $action_group->addAction( $action_transferencia );

        $this->datagrid->addActionGroup( $action_group );

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

            $repository = new TRepository( "BauRecord" );

            $properties = [
                "order" => "dataentrada",
                "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
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

                $repository = new TRepository( "PacienteRecord" );

                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                switch( $data->opcao ) {

                    case "nomepaciente":

                        $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );

                        break;

                }

                $objects = $repository->load( $criteria, FALSE );

                $this->datagrid->clear();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
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
