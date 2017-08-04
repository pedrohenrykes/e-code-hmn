<?php

class DashboardList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new TQuickForm( "form_list_dashboard" );
        $this->form->setFormTitle( "Listegem de Itens do Dashboard" );
        $this->form->class = "tform";

        $this->form->addQuickAction( "Novo", new TAction( [ "DashboardForm", "onEdit" ] ), "bs:plus-sign green" );
        $this->form->addQuickAction( "Ir para dashboard", new TAction( [ "DashboardCreate", "loadData" ] ), "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_title = new TDataGridColumn( "title", "Titulo", "center" );
        $column_dataview = new TDataGridColumn( "dataview", "View", "center" );
        $column_icon = new TDataGridColumn( "icon", "Icone", "center" );
        $column_color = new TDataGridColumn( "color", "Cor", "center" );
        $column_page = new TDataGridColumn( "page", "Página", "center" );

        $this->datagrid->addColumn( $column_title );
        $this->datagrid->addColumn( $column_dataview );
        $this->datagrid->addColumn( $column_icon );
        $this->datagrid->addColumn( $column_color );
        $this->datagrid->addColumn( $column_page );

        $column_icon->setTransformer( function($value, $object, $row)
        {
            $div = new TElement('span');
            $div->style="font-family:'FontAwesome',Helvetica;font-size:20px";

            try {

                TTransaction::open("database");

                $repo = new TRepository( "FontAwesomeModel" );

                $crit = new TCriteria();
                $crit->setProperty( "order", "id" );
                $crit->add( new TFilter( "class", "=", $value ) );

                $obj = $repo->load($crit);

                if ( !empty ( $obj ) ) {
                    foreach ( $obj as $item ) {
                        $div->add( $item->unicode );
                    }
                }

                TTransaction::close();

            } catch ( Exception $ex ) {

                TTransaction::rollback();

                new TMessage( "error", $ex->getMessage() );
            }

            return $div;
        });

        $action_edit = new TDataGridAction( [ "DashboardForm", "onEdit" ] );
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

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 90%";
        $container->add( new TXMLBreadCrumb( "menu.xml", __CLASS__ ) );
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "DashboardModel" );

            if ( empty( $param[ "order" ] ) ) {
                $param[ "order" ] = "id";
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

            $count = $repository->count($criteria);

            $this->pageNavigation->setCount($count);
            $this->pageNavigation->setProperties($param);
            $this->pageNavigation->setLimit($limit);

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
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

            $object = new DashboardModel( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage("info", "Registro apagado com sucesso!");

        } catch ( Exception $ex ) {

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
