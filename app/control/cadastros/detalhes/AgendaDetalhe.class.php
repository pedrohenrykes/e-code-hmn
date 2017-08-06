<?php

class AgendaDetalhe extends TStandardList
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

        $this->form = new BootstrapFormBuilder( "form_list_agenda" );
        $this->form->setFormTitle( "Formulário e Listam da Agenda" );
        $this->form->class = "tform";

        parent::setDatabase('database');
        parent::setActiveRecord('AgendaRecord');
        parent::setDefaultOrder('id', 'asc');

        $id                         = new THidden( "id" );
        $convenio_id                = new TDBCombo("estabelecimento_id", "database", "ConvenioRecord", "id", "nome");
        $paciente_id                = new TDBCombo("paciente_id", "database", "PacienteRecord", "id", "nome");
        $unidadeespecialidade_id    = new TDBCombo("unidadeespecialidade_id", "database", "UnidadeEspecialidadeRecord", "id", "nome");

        $horaagenda                 = new TDateTime( "horaagenda" );
        $horachegada                = new TDateTime( "horachegada" );
        $horaatendimento            = new TDateTime( "horaatendimento" );

        $horaagenda->setDatabaseMask('yyyy-mm-dd hh:ii');
        $horachegada->setDatabaseMask('yyyy-mm-dd hh:ii');
        $horaatendimento->setDatabaseMask('yyyy-mm-dd hh:ii');

        //Definindo tamanho dos campos.
        $convenio_id->setSize('30%');
        $paciente_id->setSize('30%');
        $unidadeespecialidade_id->setSize('30%');

        $horaagenda->setSize('30%');
        $horachegada->setSize('30%');
        $horaatendimento->setSize('30%');

        $horaagenda->setValue(getdate());
        $horachegada->setValue(getdate());
        $horaatendimento->setValue(getdate());

        //Criação dos campos.
        $this->form->addFields( [ new TLabel( 'Hora do Agendamento:'  ) ], [ $horaagenda      ] );
        $this->form->addFields( [ new TLabel( 'Hora de Chegada: '     ) ], [ $horachegada     ] );
        $this->form->addFields( [ new TLabel( 'Hora do Atendimento: ' ) ], [ $horaatendimento ] );

        //Botões
        $this->form->addAction( 'Buscar', new TAction( [$this, 'onSearch'] ), 'fa:search' );
        $this->form->addAction( 'Salvar', new TAction( [$this, 'onSave'] ), 'fa:save' );
        $this->form->addAction('Limpar',  new TAction([$this, 'onClear']), 'fa:eraser red');

        //Criando o DataGrid.
        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_convenio             = new TDataGridColumn( "convenio_id", "Convenio", "center", 50 );
        $column_paciente             = new TDataGridColumn( "paciente_id", "Paciente", "center" );
        $column_unidadeespecialidade = new TDataGridColumn( "unidadeespecialidade_id", "Unidade Especialidade", "center" );

        $this->datagrid->addColumn( $column_convenio );
        $this->datagrid->addColumn( $column_paciente );
        $this->datagrid->addColumn( $column_unidadeespecialidade );

        $order_convenio = new TAction( [ $this, "onReload" ] );
        $order_convenio->setParameter( "order", "convenio_id" );
        $column_convenio->setAction( $order_convenio );

        $order_paciente = new TAction( [ $this, "onReload" ] );
        $order_paciente->setParameter( "order", "estabelecimento_id" );
        $column_paciente->setAction( $order_paciente );

        $order_unidadeespecialidade = new TAction( [ $this, "onReload" ] );
        $order_unidadeespecialidade->setParameter( "order", "unidadeespecialidade_id" );
        $column_unidadeespecialidade->setAction( $order_unidadeespecialidade);

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

        try{
            //ini and records
            TTransaction::open('database');
            $object = $this->form->getData('AgendaRecord');
            $object->store();

            TTransaction::close();

            new TMessage( 'info', 'Registro salvo!');
        }

        catch (Exception $se)
        {
            new TMessage('error', $se->getMessage());
            TTransaction::rollback();
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
                $repository = new TRepository( "AgendaRecord" );
                if ( empty( $param[ "order" ] ) )
                {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }
                $limit = 10;
                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );
                if( $data->opcao == "nome" )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                }
                else if ( ( $data->opcao == "nome" || $data->opcao == "crm" ) && ( is_numeric( $data->dados ) ) )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", $data->dados . "%" ) );
                }
                else
                {
                    new TMessage( "error", "O valor informado não é valido para um " . strtoupper( $data->opcao ) . "." );
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
                new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );
            }
        }
          catch ( Exception $ex )
        {
            TTransaction::rollback();
            $this->form->setData( $data );
            new TMessage( "error", $ex->getMessage() );
        }
    }

    public function onClear($param)
    {
        $this->form->clear();
    }

    //public function onReload(){}

    //public function onDelete(){}
}
