<?php

class ExameDetail extends TStandardList
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

        $this->form = new BootstrapFormBuilder( "detail_exame" );
        $this->form->setFormTitle( "Cadastro de Exame" );

        parent::setDatabase('database');
        parent::setActiveRecord('ExameRecord');
        parent::addFilterField('nome', 'like', 'nome');

        $id     = new THidden( "id" );
        $nome   = new TEntry( "nome" );
        $tipoexame = new TDBCombo("tipoexame_id", "database", "TipoExameRecord", "id", "nome", "nome");

        $nome->style = "text-transform: uppercase;";
        $id->setSize( "38%" );
        $nome->setSize( "38%" );
        $tipoexame->setSize( "38%" );
        $tipoexame->addValidation( TextFormat::set( "Tipo do exame" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Exame:" ) ], [ $nome ] );
        $this->form->addFields( [ new TLabel( "Tipo do Exame:" ) ], [ $tipoexame ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Adicionar", new TAction( [ $this, "onSave" ] ), "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_1 = new TDataGridColumn( "nome", "Exame", "left" );
        $column_2 = new TDataGridColumn( "tipo_exame_nome", "Tipo do exame", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );

        $action_edit = new TDataGridAction( [ $this, "onEdit" ] );
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
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave( $param = null ){
        try {

            $this->form->validate();
            $object = $this->form->getData( "ExameRecord" );

            TTransaction::open( "database" );
            $object->store();
            TTransaction::close();

            $action = new TAction( [ $this, "onSearch" ] );
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

                $object = new ExameRecord($key);
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
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e){
            new TMessage('error', '<b>O Registro possui dependências! Não é permitido exclui-lo! </b>');
            TTransaction::rollback();
        }
    }
    
    
}
