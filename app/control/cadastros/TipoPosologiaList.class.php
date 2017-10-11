<?php



class TipoPosologiaList extends TStandardFormList
{
    protected $form;
    protected $datagrid; 
    protected $loaded;
    protected $pageNavigation;
    
    public function __construct(){
        parent::__construct();
        
        parent::setDatabase('database');
        parent::setActiveRecord('TipoPosologiaRecord'); // define the Active Record
        parent::setDefaultOrder('nometipoposologia', 'asc'); // define the default order
        $this->setLimit(-1); // turn off limit for datagrid
        
        $this->form = new TQuickForm('form_posologia');
        $this->form->class = 'tform';
        $this->form->setFormTitle('Tipo Posologia');
        
        $id                 = new THidden('id');
        $nome               = new TEntry('nometipoposologia');
        $qtdpordia          = new TEntry('qtdpordia');
        $antesrefeicao      = new TRadioGroup('antesrefeicao');
        $aposrefeicao       = new TRadioGroup('aposrefeicao');
        $apenasrefeicao     = new TRadioGroup('apenasrefeicao');

        $antesrefeicao->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $antesrefeicao->setLayout('horizontal');
        $aposrefeicao->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $aposrefeicao->setLayout('horizontal');
        $apenasrefeicao->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $apenasrefeicao->setLayout('horizontal');

        $antesrefeicao->setValue('NAO');
        $aposrefeicao->setValue('NAO');
        $apenasrefeicao->setValue('NAO'); 
        
        //$this->form->addQuickField('ID',    $id,    '30%');
        $this->form->addQuickField('Nome',  $nome,  '50%', new TRequiredValidator);
        $this->form->addQuickField('Quantidade por dia',  $qtdpordia,  '20%', new TRequiredValidator);
        $this->form->addQuickField('Antes das Refeições',  $antesrefeicao,  '70%', new TRequiredValidator);
        $this->form->addQuickField('Após as Refeições',  $aposrefeicao,  '70%', new TRequiredValidator);
        $this->form->addQuickField('Somente nas Refeições',  $apenasrefeicao,  '70%', new TRequiredValidator);
        $this->form->addQuickField(  '', $id  );
        
        $this->form->addQuickAction(_t('Save'), new TAction(array($this, 'onSave')), 'fa:save green');
        $this->form->addQuickAction('Limpar',  new TAction(array($this, 'onClear')), 'fa:eraser red');
        
        
        $this->datagrid = new TQuickGrid;
        $this->datagrid->style = 'width: 100%';
        
        $this->datagrid->addQuickColumn('Nome',   'nometipoposologia',  'center', '20%', new TAction(array($this, 'onReload')), array('order', 'nometipoposologia'));
        $this->datagrid->addQuickColumn('Quantidade por Dia', 'qtdpordia','left',  '20%', new TAction(array($this, 'onReload')), array('order', 'qtdpordia'));
        $this->datagrid->addQuickColumn('Antes das Refeições', 'antesrefeicao','left',  '20%', new TAction(array($this, 'onReload')), array('order', 'antesrefeicao'));
        $this->datagrid->addQuickColumn('Após as Refeições', 'aposrefeicao','left',  '20%', new TAction(array($this, 'onReload')), array('order', 'aposrefeicao'));
        $this->datagrid->addQuickColumn('Somente nas Refeições', 'apenasrefeicao','left',  '20%', new TAction(array($this, 'onReload')), array('order', 'apenasrefeicao'));
        
        $this->datagrid->addQuickAction('Edit',  new TDataGridAction(array($this, 'onEdit')),   'id', 'fa:edit blue');
        $this->datagrid->addQuickAction('Delete', new TDataGridAction(array($this, 'onDelete')), 'id', 'fa:trash red');
        
        $this->datagrid->createModel();
        
        $vbox = new TVBox;
        $vbox->add($this->form);
        $vbox->add($this->datagrid);
        
        parent::add($vbox);
    }
}






















/*

class TipoPosologiaList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "list_posologia" );
        $this->form->setFormTitle( "Listagem Tipos de Posologia" );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty( "title", "Informe os dados de acordo com a opção" );

        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        $opcao->addItems( [ "nometipoposologia" => "Tipo Posologia", "situacao" => "Situação" ] );

        $this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Novo", new TAction( [ "TipoPosologiaForm", "onEdit" ] ), "bs:plus-sign green" );
    
        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_nomedestinoobito = new TDataGridColumn( "nomedestinoobito", "Destino de Óbito", "left" );
        $column_situacao = new TDataGridColumn( "situacao", "Situação", "left" );

        $this->datagrid->addColumn( $column_nomedestinoobito );
        $this->datagrid->addColumn( $column_situacao );

        $action_edit = new TDataGridAction( [ "TipoPosologiaForm", "onEdit" ] );
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
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onReload( $param = NULL )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "TipoPosologiaRecord" );

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

    public function onSearch()
    {
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );

                $repository = new TRepository( "TipoPosologiaRecord" );

                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                switch ( $data->opcao ) {

                    case "nomedestinoobito":
                        $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                        break;
                        
                    case "situacao":
                        $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                        break;

                    default:
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

                new TMessage( "error", "Selecione uma opcao e informe os dados da busca corretamente!" );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $data );

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
                $object = new DestinoObitoRecord( $param[ "key" ] );
                $object->delete();
            TTransaction::close();

            $this->onReload();

            new TMessage( "info", "Registro apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function show()
    {
        $this->onReload();

        parent::show();
    }
}
*/