<?php

class GrauInstrucaoList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder("form_list_grau_instrucao" );
        $this->form->setFormTitle( "Listagem de Grau de Instrução" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty ( 'title', "Informe os dados de acordo com a opção" );

        $opcao->setSize( '38%' );
        $dados->setSize( '38%' );

        $opcao->addItems( ["nomegrauinstrucao" => "Grau de Instrução"] );

        $this->form->addFields( [ new TLabel( 'Opção de filtro:' ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( 'Dados da busca:' )  ], [ $dados ] );

        $this->form->addAction( 'Buscar', new TAction( [$this, 'onSearch'  ] ), 'fa:search' );
        $this->form->addAction( 'Novo'  , new TAction( ["GrauInstrucaoForm", 'onEdit'] ), 'bs:plus-sign green' );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_nomegrauinstrucao  = new TDataGridColumn( "nomegrauinstrucao", "Nome", "left" );

        $this->datagrid->addColumn( $column_nomegrauinstrucao );

        $action_edit = new TDataGridAction (["GrauInstrucaoForm", "onEdit"]);
        $action_edit->setButtonClass ( "btn btn-default" );
        $action_edit->setLabel ( "Editar" );
        $action_edit->setImage ( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField ( "id" );
        $this->datagrid->addAction ( $action_edit );

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
        // $container->add( new TXMLBreadCrumb( "menu.xml", __CLASS__ ) );
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            TTransaction::open('database');

            $object = $this->form->getData('GrauInstrucaoRecord');
            $object->store();

            TTransaction::close();

            new TMessage( 'info', 'Sucess');

        } catch (Exception $se) {

            new TMessage('error', $se->getMessage());

            TTransaction::rollback();
        }
    }

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( 'database' );

            $repository = new TRepository( 'GrauInstrucaoRecord' );

            if ( empty( $param[ 'order' ] ) ) {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( 'limit', $limit );

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

    public function onSearch()
    {
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( 'database' );

                $repository = new TRepository( 'GrauInstrucaoRecord' );

                if ( empty( $param[ 'order' ] ) ) {
                    $param[ 'order' ] = "id";
                    $param[ 'direction' ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( 'limit', $limit );

                if( $data->opcao == 'nomegrauinstrucao' ) {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                }

                $objects = $repository->load( $criteria, FALSE );

                $this->datagrid->clear();

                if ( $objects ) {
                    foreach ( $objects as $object ) {
                        $this->datagrid->addItem( $object );
                    }
                } else {
                  new TMessage( "erro", "Não há dados cadastrados!" );
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

                new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $data );

            new TMessage( 'error', $ex->getMessage() );
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

            $object = new GrauInstrucaoRecord( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage("info", "O Registro foi apagado com sucesso!");

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
