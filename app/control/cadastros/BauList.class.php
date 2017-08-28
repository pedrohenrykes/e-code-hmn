<?php

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
        $this->form->setFormTitle( "Listagem de Cadastros de B.A.U." );
        $this->form->class = "tform";

        $opcao = new TCombo( "opcao" );
        $dados = new TEntry( "dados" );

        $opcao->setDefaultOption( "..::SELECIONE::.." );
        $dados->setProperty ( "title", "Informe os dados referentes a opção" );

        $opcao->setSize( "38%" );
        $dados->setSize( "38%" );

        $opcao->addItems( [ "nome" => "Nome"] );

        $this->form->addFields( [ new TLabel( "Opção de busca:" ) ], [ $opcao ] );
        $this->form->addFields( [ new TLabel( "Dados à buscar:" )  ], [ $dados ] );

        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        // $this->form->addAction( "Novo"  , new TAction( ["BauForm", "onEdit"] ), "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome = new TDataGridColumn( "paciente_nome", "Nome", "left" );
        $column_dataentrada = new TDataGridColumn( "dataentrada", "Dia", "left" );
        $column_horaentrada = new TDataGridColumn( "horaentrada", "Hora", "center" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );

        // $action_edit = new TDataGridAction( [ "BauForm", "onEdit" ] );
        // $action_edit->setButtonClass( "btn btn-default" );
        // $action_edit->setLabel( "Editar" );
        // $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        // $action_edit->setField( "id" );
        // $this->datagrid->addAction( $action_edit );

        // $action_del = new TDataGridAction( [ $this, "onDelete" ] );
        // $action_del->setButtonClass( "btn btn-default" );
        // $action_del->setLabel( "Deletar" );
        // $action_del->setImage( "fa:trash-o red fa-lg" );
        // $action_del->setField( "id" );
        // $this->datagrid->addAction( $action_del );

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

    public function onReload()
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "BauRecord" );

            $properties = [
                "order" => "dataentrada",
                "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $dataobito           = new DateTime( $object->dataobito );
                    $horaobito           = new DateTime( $object->horaobito );
                    $dataentrada         = new DateTime( $object->dataentrada );
                    $horaentrada         = new DateTime( $object->horaentrada );
                    $dataremocao         = new DateTime( $object->dataremocao );
                    $datainternamento    = new DateTime( $object->datainternamento );
                    $datatransferencia   = new DateTime( $object->datatransferencia );
                    $dataaltahospitalar  = new DateTime( $object->dataaltahospitalar );
                    $horaaltahospitalar  = new DateTime( $object->horaaltahospitalar );
                    $declaracaoobitodata = new DateTime( $object->declaracaoobitodata );
                    $declaracaoobitohora = new DateTime( $object->declaracaoobitohora );

                    $object->dataobito           = $dataobito->format("d/m/Y");
                    $object->horaobito           = $horaobito->format("H:i");
                    $object->dataentrada         = $dataentrada->format("d/m/Y");
                    $object->horaentrada         = $horaentrada->format("H:i");
                    $object->dataremocao         = $dataremocao->format("d/m/Y");
                    $object->datainternamento    = $datainternamento->format("d/m/Y");
                    $object->datatransferencia   = $datatransferencia->format("d/m/Y");
                    $object->dataaltahospitalar  = $dataaltahospitalar->format("d/m/Y");
                    $object->horaaltahospitalar  = $horaaltahospitalar->format("H:i");
                    $object->declaracaoobitodata = $declaracaoobitodata->format("d/m/Y");
                    $object->declaracaoobitohora = $declaracaoobitohora->format("H:i");

                    $this->datagrid->addItem( $object );
                }

            }

            $criteria->resetProperties();

            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
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
        $data = $this->form->getData();

        try {

            if( !empty( $data->opcao ) && !empty( $data->dados ) ) {

                TTransaction::open( "database" );

                $repository = new TRepository( "BauRecord" );

                if ( empty( $param[ "order" ] ) ) {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }

                $limit = 10;

                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );

                switch( $data->opcao ) {

                    case "nomepaciente":
                        $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
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
                $this->pageNavigation->setProperties( $param );
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

            $this->form->setData( $data );

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

            $object = new BauRecord( $param[ "key" ] );
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
