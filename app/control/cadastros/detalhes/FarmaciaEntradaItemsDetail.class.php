<?php

class FarmaciaEntradaItemsDetail extends TStandardList
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

        $this->form = new BootstrapFormBuilder( "entrada_farmacia_items" );
        $this->form->setFormTitle( "Registrar Entrada de Produtos" );

        parent::setDatabase('database');
        parent::setActiveRecord('FarmaciaEntradaItemsRecord');
        parent::addFilterField('nome', 'like', 'nome');

        $id     = new THidden( "id" );
        $farmaciaentrada_id = new THidden( "farmaciaentrada_id" );
        $farmaciaproduto_id = new TDBCombo(
            "farmaciaproduto_id",
            "database", "FarmaciaProdutoRecord",
            "id", "nomeproduto",
            "nomeproduto"
        );
        $quantidadeentrada = new TEntry( "quantidadeentrada" );

        $farmaciaentrada_id->setValue(filter_input( INPUT_GET, "did" ));

        $key = filter_input( INPUT_GET, "id" );
        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $quantidadeentrada->addValidation( TextFormat::set( "Quantidade" ), new TRequiredValidator );
        $farmaciaproduto_id->addValidation( TextFormat::set( "Produto" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Produto:" ) ], [ $farmaciaproduto_id ] );
        $this->form->addFields( [ new TLabel( "Quantidade:" ) ], [ $quantidadeentrada ] );
        $this->form->addFields( [ $id, $farmaciaentrada_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $fk );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_1 = new TDataGridColumn( "nome_produto", "Produto", "left" );
        $column_2 = new TDataGridColumn( "quantidadeentrada", "Quantidade", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );

        $action_edit = new CustomDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $action_edit->setParameter( "did", $did );
        $this->datagrid->addAction( $action_edit );

        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $action_del->setParameter( "did", $did );
        $this->datagrid->addAction( $action_del );

        $this->datagrid->createModel();

        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave( $param ){
        try {

            $this->form->validate();
            $object = $this->form->getData( "FarmaciaEntradaItemsRecord" );

            TTransaction::open( "database" );
            $object->store();
            TTransaction::close();

            $action = new TAction( [ $this, "onReload" ] );

            $action->setParameter( "fk", $param[ "fk" ] );
            $action->setParameter( "did", $param[ "did" ] );
            new TMessage( "info", "Registro Salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }
    function onEdit($param) {
        try {
            if (isset($param['key'])) {

                $key = $param['key'];
                TTransaction::open('database');

                $object = new FarmaciaEntradaItemsRecord($key);
                $this->form->setData($object);

                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param){
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param);
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public function Delete($param){

        try{
            $key=$param['key'];

            TTransaction::open($this->database);
            $class = $this->activeRecord;
            $object = new $class($key);
            $object->delete();
            TTransaction::close();

            $this->onReload( $param );
            //new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e){
            new TMessage('error', '<b>O Registro possui dependências! Não é permitido exclui-lo! </b>');
            TTransaction::rollback();
        }
    }



    public function onReload($param = NULL)
    {
        try
        {
            if (empty($this->database))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', AdiantiCoreTranslator::translate('Database'), 'setDatabase()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            if (empty($this->activeRecord))
            {
                throw new Exception(AdiantiCoreTranslator::translate('^1 was not defined. You must call ^2 in ^3', 'Active Record', 'setActiveRecord()', AdiantiCoreTranslator::translate('Constructor')));
            }
            
            TTransaction::open($this->database);
            
            $repository = new TRepository($this->activeRecord);
            $limit = isset($this->limit) ? ( $this->limit > 0 ? $this->limit : NULL) : 10;
            $criteria = isset($this->criteria) ? clone $this->criteria : new TCriteria;
            if ($this->order)
            {
                $criteria->setProperty('order',     $this->order);
                $criteria->setProperty('direction', $this->direction);
            }
            $criteria->setProperties($param);
            $criteria->setProperty('limit', $limit);
            $criteria->add( new TFilter( "farmaciaentrada_id", "=", $param[ "did" ] ) );
            
            if ($this->formFilters)
            {
                foreach ($this->formFilters as $filterKey => $filterField)
                {
                    if (TSession::getValue($this->activeRecord.'_filter_'.$filterField))
                    {
                        $criteria->add(TSession::getValue($this->activeRecord.'_filter_'.$filterField));
                    }
                }
            }
            
            $objects = $repository->load($criteria, FALSE);
            
            if (is_callable($this->transformCallback))
            {
                call_user_func($this->transformCallback, $objects, $param);
            }
            
            $this->datagrid->clear();
            if ($objects)
            {
                foreach ($objects as $object)
                {
                    $this->datagrid->addItem($object);
                }
            }
            
            $criteria->resetProperties();
            $count= $repository->count($criteria);
            
            if (isset($this->pageNavigation))
            {
                $this->pageNavigation->setCount($count);
                $this->pageNavigation->setProperties($param);
                $this->pageNavigation->setLimit($limit);
            }
            
            TTransaction::close();
            $this->loaded = true;
        }
        catch (Exception $e)
        {
            new TMessage('error', $e->getMessage());
            TTransaction::rollback();
        }
    }
    
    
}
