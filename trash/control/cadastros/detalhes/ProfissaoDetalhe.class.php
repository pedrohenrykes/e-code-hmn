<?php

class ProfissaoDetalhe extends TWindow
{

    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle('Profissões');
        parent::setSize(0.600, 0.800);

        $redstar = '<font color=red><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_profissao" );
        $this->form->setFormTitle( "Formulário de Profissões" );
        $this->form->class = "tform";

        $id            = new THidden( "id" );
        $nomeprofissao = new TEntry ( "nomeprofissao" );

        $id->setSize('38%');
        $nomeprofissao->setSize('38%');

        $this->form->addFields( [ new TLabel( "Nome Profissão: $redstar" ) ], [ $nomeprofissao ] );

        $this->form->addAction( 'Limpar', new TAction( [$this, 'onClear' ] ), 'fa:eraser red');
        $this->form->addAction( 'Salvar', new TAction( [$this, 'onSave'  ] ), 'fa:save' );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_nomeprofissao = new TDataGridColumn( "nomeprofissao", "Nome", "left");

        $this->datagrid->addColumn( $column_nomeprofissao );

        $order_nomeprofissao = new TAction( [ $this, "onReload" ] );
        $order_nomeprofissao->setParameter( "order", "nomeprofissao" );
        $column_nomeprofissao->setAction( $order_nomeprofissao );

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

            $this->form->validate();

            TTransaction::open( 'database' );

            $object = $this->form->getData( 'ProfissaoRecord' );

            $object->store();

            TTransaction::close();
             $action = new TAction( [ $this, 'onReload' ] );

            new TMessage( 'info', 'Registro salvo com sucesso!', $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>' . $ex->getMessage() );
        }
    }

    public function onClear($param)
    {
        $this->form->clear();
    }

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( 'database' );

            $repository = new TRepository( 'ProfissaoRecord' );

            if ( empty( $param[ 'order' ] ) )
            {
                $param[ 'order' ] = 'id';
                $param[ 'direction' ] = 'asc';
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

            new TMessage( 'error', $ex->getMessage() );
        }
    }
     function Delete( $param = NULL )
    {
        try {

            TTransaction::open( 'database' );

            $object = new ProfissaoRecord( $param[ 'key' ] );

            $object->delete();

            TTransaction::close();

            $this->onReload();

            new TMessage( 'info', 'O Registro foi apagado com sucesso!' );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', $ex->getMessage() );
        }
    }

    public function onDelete($param)
    {
        try {

            TTransaction::open( 'database' );
                $object = new ProfissaoRecord( $param[ 'key' ] );
                $object->delete();
            TTransaction::close();

                $this->onReload();

            new TMessage( 'info', 'O Registro foi apagado com sucesso!' );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( 'error', $ex->getMessage() );
        }
    }

    public function show()
    {
        $this->onReload();
        parent::show();
    }
}
