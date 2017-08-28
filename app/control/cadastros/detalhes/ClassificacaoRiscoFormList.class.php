<?php

class ClassificacaoRiscoDetalhe extends TStandardList
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

        $this->form = new BootstrapFormBuilder( "form_list_classificacao_risco" );
        $this->form->setFormTitle( "<b>Detalhe Classificação de Risco</b>" );
        $this->form->class = "tform";
        
        parent::setDatabase('database');
        parent::setActiveRecord('ClassificacaoRiscoRecord');
        parent::setDefaultOrder('id', 'asc');

        $redstar = '<font color="red"><b>*</b></font>';
        
        $id                     = new THidden( "id" );
        $paciente_id            = new TDBCombo("paciente_id", "database", "PacienteRecord", "id", "nome");
        //$tipoclassificacaorisco_id = new TDBCombo("tipoclassificacaorisco_id", "database", "TipoClassificacaoRiscoRecord", "id", "");
        //$bau_id                    = new TDBCombo("bau_id", "database", "BauRecord", "id", "");
        //$enfermeiro_id             = new TDBCombo("enfermeiro_id", "database", "", "id", ");
        $dataclassificacao      = new TDate("dataclassificacao");
        $horaclassificacao      = new TEntry("horaclassificacao");
        $horaclassificacao->setProperty('type', 'time');
        $pressaoarterial        = new TEntry("pressaoarterial");
        $frequenciacardiaca     = new TEntry("frequenciacardiaca");
        $frequenciarespiratoria = new TEntry("frequenciarespiratoria");
        $temperatura            = new TEntry("temperatura");
        $spo2                   = new TEntry("spo2");
        $htg                    = new TEntry("htg");
        $dor                    = new TEntry("dor");
        $observacoes            = new TEntry("observacoes");
        $queixaprincipal        = new TEntry("queixaprincipal");

        //---- Field Width. ----
        $paciente_id->setSize('38%');        
        $dataclassificacao->setSize('30%');
        $horaclassificacao->setSize('30%');
        $pressaoarterial->setSize('30%');
        $frequenciacardiaca->setSize('30%');
        $frequenciarespiratoria->setSize('30%');
        $temperatura->setSize('30%');
        $spo2->setSize('30%');
        $htg->setSize('30%');
        $dor->setSize('30%');
        $observacoes->setSize('30%');
        $queixaprincipal->setSize('30%');
        
        //$tipoclassificacaorisco_id->setSize('30%');
        //$bau_id->setSize('30%');
        //$enfermeiro_id->setSize('30%');
        
        $dataclassificacao->setValue(getdate());
        $horaclassificacao->setValue(getdate());
        
        $items = [];
        TTransaction::open('database');
                $repository = new TRepository('PacienteRecord');
                $criteria = new TCriteria;
                $criteria->setProperty('order', 'nomepaciente');

                $cadastros = $repository->load($criteria);

                foreach ($cadastros as $object) 
                {
                    $items[$object->id] = $object->nomepaciente;
                }
                $paciente_id->addItems($items);
        TTransaction::close(); 

        //---- Create Fields. ----
        $this->form->addFields( [ new TLabel( "Paciente: $redstar"       ) ], [ $paciente_id            ]);
        $this->form->addFields( [ new TLabel( "Data Classificação: $redstar"      ) ], [ $dataclassificacao      ] );
        $this->form->addFields( [ new TLabel( 'Hora Classificação:'      ) ], [ $horaclassificacao      ] );
        $this->form->addFields( [ new TLabel( 'Pressão Arterial:'        ) ], [ $pressaoarterial        ] );
        $this->form->addFields( [ new TLabel( 'Frequência Cardíaca:'     ) ], [ $frequenciacardiaca     ] );
        $this->form->addFields( [ new TLabel( 'Frequência Respiratória:' ) ], [ $frequenciarespiratoria ] );
        $this->form->addFields( [ new TLabel( 'Temperatura:'             ) ], [ $temperatura            ] );
        $this->form->addFields( [ new TLabel( 'SPO2:'                    ) ], [ $spo2                   ] );
        $this->form->addFields( [ new TLabel( 'HTG:'                     ) ], [ $htg                    ] );
        $this->form->addFields( [ new TLabel( 'DOR:'                     ) ], [ $dor                    ] );
        $this->form->addFields( [ new TLabel( 'Observação:'              ) ], [ $observacoes            ] );
        $this->form->addFields( [ new TLabel( 'Queixa Principal:'        ) ], [ $queixaprincipal        ] );
        
        //$this->form->addFields( [ new TLabel( 'BAU:'                     ) ], [ $bau_id                    ] );
        //$this->form->addFields( [ new TLabel( 'Classificação de Risco:'                     ) ], [ $tipoclassificacaorisco_id ] );
        //$this->form->addFields( [ new TLabel( ':'                     ) ], [ $enfermeiro_id             ] );
        
        $this->form->addFields([new TLabel('<font color=red><b>* Campos Obrigatórios </b></font>'), []] );

        //---- Buttons ----
        $this->form->addAction('Buscar', new TAction( [$this, 'onSearch'] ), 'fa:search'    );
        $this->form->addAction('Salvar', new TAction( [$this, 'onSave'  ] ), 'fa:save'      );
        $this->form->addAction('Limpar', new TAction( [$this, 'onClear' ] ), 'fa:eraser red');

        //---- Create DataGrid ----
        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );
        
        $column_paciente                = new TDataGridColumn("paciente_id", "Nome Paciente", "left");
        $column_queixaprincipal         = new TDataGridColumn("queixaprincipal", "Queixa Principal", "left");
        $column_dataclassificacao       = new TDataGridColumn("dataclassificacao", "Data Classificação", "left");
        $column_horaclassificacao       = new TDataGridColumn("horaclassificacao", "Hora Classificação", "left");
        //$column_tipoclassificacaorisco = new TDataGridColumn("tipoclassificacaorisco_id", "Tipo Classificacao Risco", "left");
        //$column_bau                    = new TDataGridColumn("bau_id", "Bau", "left");
        //$column_enfermeiro             = new TDataGridColumn("enfermeiro_id", "Enfermeiro", "left");

        $this->datagrid->addColumn( $column_paciente );
        $this->datagrid->addColumn( $column_queixaprincipal );
        $this->datagrid->addColumn( $column_horaclassificacao );
        $this->datagrid->addColumn( $column_dataclassificacao );
        //$this->datagrid->addColumn( $column_tipoclassificacaorisco );
        //$this->datagrid->addColumn( $column_bau );
        //$this->datagrid->addColumn( $column_enfermeiro );
        
        $order_paciente = new TAction( [ $this, "onReload" ] );
        $order_paciente->setParameter( "order", "paciente_id" );
        $column_paciente->setAction( $order_paciente );

        $order_queixaprincipal = new TAction( [ $this, "onReload" ] );
        $order_queixaprincipal->setParameter( "order", "queixaprincipal" );
        $column_queixaprincipal->setAction( $order_queixaprincipal );

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
        //$container->add( new TXMLBreadCrumb( "menu.xml", __CLASS__ ) );
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave()
    {
        try{    
            TTransaction::open('database');
                $object = $this->form->getData('ClassificacaoRiscoRecord');
                $object->store();
            TTransaction::close();

            new TMessage( 'info', 'Registro salvo!');
        }

        catch (Exception $se){
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
                $repository = new TRepository( "ClassificacaoRiscoRecord" );
                if ( empty( $param[ "order" ] ) )
                {
                    $param[ "order" ] = "id";
                    $param[ "direction" ] = "asc";
                }
                $limit = 10;
                $criteria = new TCriteria();
                $criteria->setProperties( $param );
                $criteria->setProperty( "limit", $limit );
                if( $data->opcao == "nome" && ( is_numeric( $data->dados ) ) )
                {
                    $criteria->add( new TFilter( $data->opcao, "LIKE", "%" . $data->dados . "%" ) );
                }
                else
                {
                    // new TMessage( "error", "O valor informado não é valido para um " . strtoupper( $data->opcao ) . "." );
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
                $this->pageNavigation->setCount( $count );
                $this->pageNavigation->setProperties( $param );
                $this->pageNavigation->setLimit( $limit );
                TTransaction::close();
                $this->form->setData( $data );
                $this->loaded = true;
            }
            else
            {
                $this->onReload();
                $this->form->setData( $data );
                // new TMessage( "error", "Selecione uma opção e informe os dados da busca corretamente!" );
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

}
