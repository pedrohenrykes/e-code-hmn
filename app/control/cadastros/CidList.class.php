<?php

// Revisado 18.05.17

class CidList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();
        // Criacao do formulario
        $this->form = new BootstrapFormBuilder( "form_list_cid" );
        $this->form->setFormTitle( "Listagem de C.I.D." );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        // Definicao de propriedades dos campos
        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty( "title", "Informe os dados de acordo com a opção" );
        // $dados->forceUpperCase();

        // Definicao dos tamanhos do campos
        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        // Definicao das opçoes dos combos
        $opcao->addItems( [ "codigocid" => "Código", "nomecid" => "Nome" ] );
        $this->form->addFields( [ new TLabel( "Opção de filtro:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados da busca:" ) ], [ $dados ] );



        // Criacao dos botoes com sua determinada acoes no fomulario
        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Novo", new TAction( [ "CidForm", "onEdit" ] ), "bs:plus-sign green" );

        //Criacao do datagrid de listagem de dados
        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_codigocid = new TDataGridColumn( "codigocid", "Código", "left" );
        $column_nomecid = new TDataGridColumn( "nomecid", "Nome", "left" );

        $this->datagrid->addColumn( $column_codigocid );
        $this->datagrid->addColumn( $column_nomecid );

        $order_codigocid = new TAction( [ $this, "onReload" ] );
        $order_codigocid->setParameter( "order", "codigocid" );
        $column_codigocid->setAction( $order_codigocid );

        $order_nome = new TAction( [ $this, "onReload" ] );
        $order_nome->setParameter( "order", "nomecid" );
        $column_nomecid->setAction( $order_nome );

        $action_edit = new TDataGridAction( [ "CidForm", "onEdit" ] );
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

        //Exibicao do datagrid
        $this->datagrid->createModel();

        //Criacao do navedor de paginas do datagrid
        $this->pageNavigation = new TPageNavigation();
        $this->pageNavigation->setAction( new TAction( [ $this, "onReload" ] ) );
        $this->pageNavigation->setWidth( $this->datagrid->getWidth() );

        // Criacao do container que recebe o formulario
        $container = new TVBox();
        $container->style = "width: 90%";
        // $container->add( new TXMLBreadCrumb( "menu.xml", __CLASS__ ) );
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );
        // Adicionando o container com o form a pagina
        parent::add( $container );
    }

    public function onReload( $param = NULL )
    {
        try
        {
            // Abrindo a conexao com o banco de dados
            TTransaction::open( "database" );
            // Criando um repositorio para armazenar temporariamente os dados do banco
            $repository = new TRepository( "CidRecord" );
            if ( empty( $param[ "order" ] ) )
            {
                $param[ "order" ] = "id";
                $param[ "direction" ] = "asc";
            }
            $limit = 10;
            // Criando um criterio de busca no banco de dados
            $criteria = new TCriteria();
            $criteria->setProperties( $param );
            $criteria->setProperty( "limit", $limit );
            // Buscando os dados no banco de acordo com os criterios passados
            $objects = $repository->load( $criteria, FALSE );
            // Limpando o datagrid
            $this->datagrid->clear();
            // Se existirem dados no banco, o datagrid sera prenchido por esse foreach
            if ( !empty( $objects ) )
            {
                foreach ( $objects as $object )
                {
                    $this->datagrid->addItem( $object );
                }
            }
            $criteria->resetProperties();
            // Salvando a contagem dos registros que estam no repositorio
            $count = $repository->count($criteria);
            $this->pageNavigation->setCount($count); // Definindo quantos registros tera por pagina do datagrid
            $this->pageNavigation->setProperties($param); // Definindo os paramentros de organizacao dos dados por pagina
            $this->pageNavigation->setLimit($limit); // Definindo o limite de registros por pagina do datagrid
            // Fechando a conexao com o banco de dados
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
                $repository = new TRepository( "CidRecord" );
                if ( empty( $param[ "order" ] ) )
                {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }
                $limit = 10;
                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );
                if( $data->opcao == "nomecid" )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                }
                else if ( $data->opcao == "codigocid" )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                }
                else
                {
                    // new TMessage( "error", "O valor informado nao é valido para um " . strtoupper( $data->opcao ) . "." );
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
                $this->pageNavigation->setCount( $count ); // count of records
                $this->pageNavigation->setProperties( $param ); // order, page
                $this->pageNavigation->setLimit( $limit ); //Limita a quantidade de registros
                TTransaction::close();
                $this->form->setData( $data );
                $this->loaded = true;
            }
            else
            {
                $this->onReload();
                $this->form->setData( $data );
                // new TMessage( "error", "Selecione uma opcao e informe os dados da busca corretamente!" );
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
            //Criacao das acoes a serem executadas na mensagem de exclusao
            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            //Definicao sos parametros de cada acao
            $action1->setParameter( "key", $param[ "key" ] );
            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    function Delete( $param = NULL )
    {
        try
        {
            TTransaction::open( "database" );
            $object = new CidRecord( $param[ "key" ] );
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
