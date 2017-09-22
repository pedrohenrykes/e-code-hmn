<?php

class PacienteList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "list_paciente" );
        $this->form->setFormTitle( "Listagem de Pacientes" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty ( "title", "Informe os dados referentes a opção" );

        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        $opcao->addItems([
            "numerosus" => "Cartão SUS",
            "nomepaciente" => "Nome",
            "numerorg" => "RG",
            "numerocpf" => "CPF"
        ]);

        $opcao->setValue( "numerosus" );

        $this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        $this->form->addAction( "Buscar", new TAction( [$this, "onSearch"  ] ), "fa:search" );
        $this->form->addAction( "Novo"  , new TAction( ["PacienteForm", "onEdit"] ), "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_nomepaciente = new TDataGridColumn( "nomepaciente", "Nome", "left" );
        $column_numerosus    = new TDataGridColumn( "numerosus", "Cartão SUS", "left");
        $column_numerorg     = new TDataGridColumn( "numerorg", "RG", "left");
        $column_numerocpf    = new TDataGridColumn( "numerocpf", "CPF", "left");
        $column_telcelular   = new TDataGridColumn( "telcelular", "Telefone Celular", "left" );

        $this->datagrid->addColumn( $column_nomepaciente );
        $this->datagrid->addColumn( $column_numerosus );
        $this->datagrid->addColumn( $column_numerorg );
        $this->datagrid->addColumn( $column_numerocpf );
        $this->datagrid->addColumn( $column_telcelular );


        $action_edit = new CustomDataGridAction ( [ "PacienteForm", "onEdit" ] );
        $action_edit->setButtonClass ( "btn btn-default" );
        $action_edit->setLabel ( "Editar" );
        $action_edit->setImage ( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField ( "id" );
        $this->datagrid->addAction ( $action_edit );

        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $this->datagrid->addAction( $action_del );

        $action_bau = new CustomDataGridAction( [ "BauDetail", "onReload" ] );
        $action_bau->setButtonClass( "btn btn-dafault" );
        $action_bau->setLabel( "B.A.U." );
        $action_bau->setImage( "fa:clipboard green fa-lg" );
        $action_bau->setField( "id" );
        $action_bau->setFk( "id" );
        $this->datagrid->addAction( $action_bau );

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

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "PacienteRecord" );

            if ( empty( $param[ "order" ] ) ) {
                $param[ "order" ] = "nomepaciente";
                $param[ "direction" ] = "asc";
            }

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );

            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();

            if ( !empty( $objects ) ) {
                foreach ( $objects as $object ) {
                    $this->datagrid->addItem( $object );
                }
            }

            $criteria->resetProperties();

            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $param );
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
        try {

            $data = $this->form->getData();

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );

                $repository = new TRepository( "PacienteRecord" );

                $properties = [
                    "order" => "nomepaciente",
                    "direction" => "asc"
                ];

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $properties );
                $criteria->setProperty( "limit", $limit );

                switch( $data->opcao ) {

                    case "nomepaciente":
                        $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                        break;

                    case "numerocpf":
                        $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                        break;

                    default:
                        if ( is_numeric( $data->dados ) ) {
                            $criteria->add( new TFilter( $data->opcao, "=", $data->dados ) );
                        } else {
                            new TMessage( "erro", "Para a opção selecionada, informe apenas valores numéricos." );
                        }

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
                $this->pageNavigation->setProperties( $properties );
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

            new TMessage( "erro", $ex->getMessage() );

        }
    }

    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) ) {

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameter( "key", $param[ "key" ] );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    function Delete( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $object = new PacienteRecord( $param[ "key" ] );
            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "erro", $ex->getMessage() );
        }
    }

    public function show()
    {
        $this->onReload();

        parent::show();
    }

}
