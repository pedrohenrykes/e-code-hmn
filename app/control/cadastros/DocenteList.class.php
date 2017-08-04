<?php
/**
 *
 * @author Danilo Cunha
 */

class DocenteList extends TStandardList
{
    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $loaded;

    public function __construct()
    {
        parent::__construct();

        parent::setDatabase('database');
        parent::setActiveRecord('DocenteRecord');
        parent::setDefaultOrder('id', 'asc');

        $this->form = new BootstrapFormBuilder("form_list_docente");
        $this->form->setFormTitle( "Listagem de Docentes" );
        $this->form->class = "tform";

        $opcao = new TCombo("opcao");
        $dados = new TEntry("dados");

        $opcao->addItems( [ "nomedocente" => "Docente", "chapa" => "Chapa" ] );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty( "title", "Informe os dados de acordo com a opção" );

        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        $this->form->addFields( [ new TLabel( "Opção de filtro:" ) ] ,  [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados da busca:" ) ] , [ $dados ] );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Novo", new TAction( [ "DocenteForm", "onEdit" ] ), "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper(new TDataGrid());
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight(320);

        $columm_nome = new TDataGridColumn("nomedocente", "Docente", "left");
        $column_chapa = new TDataGridColumn("chapa", "Chapa", "left");
        $columm_email = new TDataGridColumn("email", "E-mail", "left");
        $columm_contato = new TDataGridColumn("contato", "Telefone", "left");

        $this->datagrid->addColumn($columm_nome);
        $this->datagrid->addColumn($column_chapa);
        $this->datagrid->addColumn($columm_email);
        $this->datagrid->addColumn($columm_contato);

        $action_edit = new TDataGridAction( [ "DocenteForm", "onEdit" ] );
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

    public function onSearch()
    {
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );

                $repository = new TRepository( "DocenteRecord" );

                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                if( $data->opcao == "nomedocente" ) {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                } else if ( ( $data->opcao == "chapa" ) && ( is_numeric( $data->dados ) ) ) {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                } else {
                    new TMessage( "error", "O valor informado não é valido." );
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

            new TMessage( "error", $ex->getMessage() );
        }
    }
}
