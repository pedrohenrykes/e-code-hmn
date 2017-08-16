<?php

ini_set('display_errors', 1);
ini_set('display_startup_erros', 1);
error_reporting(E_ALL);

//use Adianti\Database\TFilter1;
//use Adianti\Widget\Datagrid\TDatagridTables;
//A

class BauList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;
    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "list_bau" );
        $this->form->setFormTitle( "Listagem de BAU" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty( "title", "Informe os dados de acordo com a opção" );
        // $dados->forceUpperCase();

        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        $opcao->addItems( [ "nome" => "Nome"] );
        $this->form->addFields( [ new TLabel( "Opção de filtro:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados da busca:" ) ], [ $dados ] );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Novo", new TAction( [ "BauForm", "onEdit" ] ), "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_1 = new TDataGridColumn( "paciente_nome", "Nome", "left" );
        $column_2 = new TDataGridColumn( "dataentrada", "Dia", "left" );
        $column_3 = new TDataGridColumn( "horaentrada", "Hora", "center" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );

        $order_nome = new TAction( [ $this, "onReload" ] );
        $order_nome->setParameter( "order", "nome" );
        $column_1->setAction( $order_nome );

        $order_dataentrada = new TAction( [ $this, "onReload" ] );
        $order_dataentrada->setParameter( "order", "dataentrada" );
        $column_2->setAction( $order_dataentrada );


        $action_edit = new TDataGridAction( [ "BauForm", "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $this->datagrid->addAction( $action_edit );

        $action_del = new TDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $this->datagrid->addAction( $action_del );
        /*
        $action_1 = new TDataGridAction( [ "DoencaBaseDetalhe", "onReload" ] );
        $action_1->setButtonClass( "btn btn-default" );
        $action_1->setLabel( "Doenca Base" );
        $action_1->setImage( "fa:heart-o fa-fw" );
        $action_1->setField( "id" );
        $action_1->setFk( "id" );
        
        $action_group = new TDataGridActionGroup('Ações', 'bs:th');
        $action_group->addAction($action_1);

        $this->datagrid->addActionGroup($action_group);
        */

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
        try
        {

            TTransaction::open( "database" );


            $repository = new TRepository( "BauRecord" );
            if ( empty( $param[ "order" ] ) )
            {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }
            $limit = 10;


            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );
            //$criteria->add(new TFilter('medico_id', '=', TSession::getValue('medico_id')));

            $objects = $repository->load( $criteria, FALSE );

            $this->datagrid->clear();


            if ( !empty( $objects ) )
            {
                foreach ( $objects as $object )
                {
                    //$object->datadiagnostico = TDate::date2br( $object->datadiagnostico );
                    $this->datagrid->addItem( $object );
                }
            }
            $criteria->resetProperties();

            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();
            $this->loaded = true;
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );
        }
    }
    public function onSearch()
    {
        $data = $this->form->getData();
        try
        {
            if( !empty( $data->opcao ) && !empty( $data->dados ) )
            {
                TTransaction::open( "database" );
                $repository = new TRepository( "BauRecord" );
                if ( empty( $param[ "order" ] ) )
                {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }
                $limit = 10;
                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );
                if( $data->opcao == "nome" && ( is_numeric( $data->dados ) ) )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                }
                else
                {
                    // new TMessage( "error", "O valor informado não é valido para um " . strtoupper( $data->opcao ) . "." );
                }
                $objects = $repository->load( $criteria, FALSE );
                $this->datagrid->clear();
                if ( $objects )
                {
                    foreach ( $objects as $object )
                    {
                        $this->datagrid->addItem( $object );
                    }
                }
                $criteria->resetProperties();
                $count = $repository->count( $criteria );
                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );
                TTransaction::close();
                $this->form->setData( $data );
                $this->loaded = true;
            }
            else
            {
                $this->onReload();
                $this->form->setData( $data );
                // new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );
            }
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            $this->form->setData( $data );
            new TMessage( "error", $ex->getMessage() );
        }
    }
    public function onDelete( $param = NULL )
    {
        if( isset( $param[ "key" ] ) )
        {

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameter( "key", $param[ "key" ] );
            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }
    function Delete( $param = NULL )
    {
        try
        {
            TTransaction::open( "dbsic" );
            $object = new BauRecord( $param[ "key" ] );
            $object->delete();
            TTransaction::close();
            $this->onReload();
            new TMessage("info", "Registro apagado com sucesso!");
        }
        catch ( Exception $ex )
        {
            TTransaction::rollback();
            new TMessage("error", $ex->getMessage());
        }
    }
    public function show()
    {
        $this->onReload();
        parent::show();
    }
}
