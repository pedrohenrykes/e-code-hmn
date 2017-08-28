<?php

class ClassificacaoRiscoFormList extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_list_classificacao_risco" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                        = new THidden( "id" );
        $paciente_id               = new THidden( "paciente_id" );
        $bau_id                    = new THidden( "bau_id" );
        $dataclassificacao         = new TDate("dataclassificacao");
        $horaclassificacao         = new TDateTime("horaclassificacao");
        $paciente_nome             = new TEntry("paciente_nome");
        $pressaoarterial           = new TEntry("pressaoarterial");
        $frequenciacardiaca        = new TEntry("frequenciacardiaca");
        $frequenciarespiratoria    = new TEntry("frequenciarespiratoria");
        $temperatura               = new TEntry("temperatura");
        $spo2                      = new TEntry("spo2");
        $htg                       = new TEntry("htg");
        $dor                       = new TEntry("dor");
        $observacoes               = new TEntry("observacoes");
        $queixaprincipal           = new TEntry("queixaprincipal");

        $tipoclassificacaorisco_id = new TDBCombo("tipoclassificacaorisco_id", "database", "TipoClassificacaoRiscoRecord", "id", "");
        $enfermeiro_id             = new TDBCombo("enfermeiro_id", "database", "", "id", "");

        $fk = filter_input( INPUT_GET, "fk" );

        try {

            TTransaction::open( "database" );

            $paciente = new PacienteRecord( $fk );

            if( isset( $paciente ) ){
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );
            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            new TMessage( "error", "Não foi possível carregar os dados do paciente.<br><br>" . $ex->getMessage() );

        }

        $dataclassificacao     ->setSize('38%');
        $horaclassificacao     ->setSize('38%');
        $pressaoarterial       ->setSize('38%');
        $frequenciacardiaca    ->setSize('38%');
        $frequenciarespiratoria->setSize('38%');
        $temperatura           ->setSize('38%');
        $spo2                  ->setSize('38%');
        $htg                   ->setSize('38%');
        $dor                   ->setSize('38%');
        $observacoes           ->setSize('38%');
        $queixaprincipal       ->setSize('38%');

        $tipoclassificacaorisco_id->setDefaultOption( "..::SELECIONE::.." );
        $enfermeiro_id            ->setDefaultOption( "..::SELECIONE::.." );

        $dataclassificacao->setMask( "dd/mm/yyyy" );
        $dataclassificacao->setDatabaseMask("yyyy-mm-dd");
        $horaclassificacao->setMask( "hh:ii" );

        $dataclassificacao->setValue( date( "d/m/Y" ) );
        $horaclassificacao->setValue( date( "H:i" ) );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $label01 = new RequiredTextFormat( [ "Nome do Paciente", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Data da Avaliação", "#F00", "bold" ] );

        $paciente_id      ->addValidation( $label01->getText(), new TRequiredValidator );
        $dataclassificacao->addValidation( $label01->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Nome do Paciente: {$redstar}" ) ], [ $paciente_id ]);
        $this->form->addFields( [ new TLabel( "Data da Avaliação: {$redstar}" ) ], [ $dataclassificacao ] );
        $this->form->addFields( [ new TLabel( 'Hora da Avaliação:' ) ], [ $horaclassificacao ] );
        $this->form->addFields( [ new TLabel( 'Pressão Arterial:' ) ], [ $pressaoarterial ] );
        $this->form->addFields( [ new TLabel( 'Frequência Cardíaca:' ) ], [ $frequenciacardiaca ] );
        $this->form->addFields( [ new TLabel( 'Frequência Respiratória:' ) ], [ $frequenciarespiratoria ] );
        $this->form->addFields( [ new TLabel( 'Temperatura:' ) ], [ $temperatura ] );
        $this->form->addFields( [ new TLabel( 'SPO2:' ) ], [ $spo2 ] );
        $this->form->addFields( [ new TLabel( 'HTG:' ) ], [ $htg ] );
        $this->form->addFields( [ new TLabel( 'DOR:' ) ], [ $dor ] );
        $this->form->addFields( [ new TLabel( 'Observações:' ) ], [ $observacoes ] );
        $this->form->addFields( [ new TLabel( 'Queixa Principal:' ) ], [ $queixaprincipal ] );

        // TODO continuar a edição a partir daqui
        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "PacienteList", "onReload" ] ), "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome = new TDataGridColumn( "paciente_nome", "Nome", "left" );
        $column_dataentrada = new TDataGridColumn( "dataentrada", "Dia", "left" );
        $column_horaentrada = new TDataGridColumn( "horaentrada", "Hora", "center" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataentrada );
        $this->datagrid->addColumn( $column_horaentrada );

        $action_edit = new CustomDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_edit );

        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $this->datagrid->addAction( $action_del );

        $action_avaliacao = new CustomDataGridAction( [ "ClassificacaoRiscoFormList", "onReload" ] );
        $action_avaliacao->setButtonClass( "btn btn-default" );
        $action_avaliacao->setLabel( "Avaliação" );
        $action_avaliacao->setImage( "fa:stethoscope green fa-lg" );
        $action_avaliacao->setField( "id" );
        $this->datagrid->addAction( $action_avaliacao );

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

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauRecord" );

        try {

            $this->form->validate();

            TTransaction::open( "database" );

            unset( $object->alta );
            unset( $object->obito );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "BauFormList", "onReload" ] );
            $action->setParameter( "fk", $param[ "fk" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $object );

            foreach ( $this->changeFields as $field ) {
                self::onChangeAction([
                    "_field_name" => $field,
                    $field => $object->$field
                ]);
            }

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }

    public function onEdit( $param = null )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new BauRecord( $param[ "key" ] );

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

                $this->onReload( $param );

                $this->form->setData( $object );

                TTransaction::close();

            }

            foreach ( $this->changeFields as $field ) {
                self::onChangeAction([
                    "_field_name" => $field,
                    $field => ( isset( $param[ "key" ] ) ? $object->$field : "N" )
                ]);
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }

    public function onDelete( $param = null )
    {
        if( isset( $param[ "key" ] ) ) {

            $param = [
                "key" => $param[ "key" ],
                "fk"  => $param[ "fk" ]
            ];

            $action1 = new TAction( [ $this, "Delete" ] );
            $action2 = new TAction( [ $this, "onReload" ] );

            $action1->setParameters( $param );
            $action2->setParameters( $param );

            new TQuestion( "Deseja realmente apagar o registro?", $action1, $action2 );
        }
    }

    public function Delete( $param = null )
    {
        try {

            TTransaction::open( "database" );

            $object = new BauRecord( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            $this->onReload( $param );

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReload( $param = null )
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
            $criteria->add( new TFilter( "paciente_id", "=", $param[ "fk" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $dataentrada         = new DateTime( $object->dataentrada );
                    $horaentrada         = new DateTime( $object->horaentrada );

                    $object->dataentrada         = $dataentrada->format("d/m/Y");
                    $object->horaentrada         = $horaentrada->format("H:i");

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

            foreach ( $this->changeFields as $field ) {
                self::onChangeAction([
                    "_field_name" => $field,
                    $field => "N"
                ]);
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

    public static function onChangeAction( $param = null )
    {
        $object = new StdClass;

        $fieldName = $param[ "_field_name" ];

        switch ( $fieldName ) {

            case "internamentolocal":

                if( $param[ $fieldName ] == "S" ) {
                    TQuickForm::showField( "form_list_bau", "datainternamento" );
                } else {
                    $object->datainternamento = "";
                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "datainternamento" );
                }

                break;

            case "remocao":

                if( $param[ $fieldName ] == "S" ) {
                    TQuickForm::showField( "form_list_bau", "dataremocao" );
                    TQuickForm::showField( "form_list_bau", "localremocao_id" );
                } else {
                    $object->dataremocao = "";
                    $object->localremocao_id = "..::SELECIONE::..";
                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "dataremocao" );
                    TQuickForm::hideField( "form_list_bau", "localremocao_id" );
                }

                break;

            case "transferencia":

                if( $param[ $fieldName ] == "S" ) {
                    TQuickForm::showField( "form_list_bau", "datatransferencia" );
                    TQuickForm::showField( "form_list_bau", "localtransferencia_id" );
                } else {
                    $object->datatransferencia = "";
                    $object->localtransferencia_id = "..::SELECIONE::..";
                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "datatransferencia" );
                    TQuickForm::hideField( "form_list_bau", "localtransferencia_id" );
                }

                break;

            case "alta":

                if( $param[ $fieldName ] == "S" ) {

                    TQuickForm::showField( "form_list_bau", "medicoalta_id" );
                    TQuickForm::showField( "form_list_bau", "horaaltahospitalar" );
                    TQuickForm::showField( "form_list_bau", "dataaltahospitalar" );
                    TQuickForm::showField( "form_list_bau", "tipoaltahospitalar_id" );

                } else {

                    $object->medicoalta_id = "";
                    $object->horaaltahospitalar = "";
                    $object->dataaltahospitalar = "";
                    $object->tipoaltahospitalar_id = "";

                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "medicoalta_id" );
                    TQuickForm::hideField( "form_list_bau", "horaaltahospitalar" );
                    TQuickForm::hideField( "form_list_bau", "dataaltahospitalar" );
                    TQuickForm::hideField( "form_list_bau", "tipoaltahospitalar_id" );

                }

                break;

            case "obito":

                if( $param[ $fieldName ] == "S" ) {

                    TQuickForm::showField( "form_list_bau", "dataobito" );
                    TQuickForm::showField( "form_list_bau", "horaobito" );
                    TQuickForm::showField( "form_list_bau", "declaracaoobitodata" );
                    TQuickForm::showField( "form_list_bau", "declaracaoobitohora" );
                    TQuickForm::showField( "form_list_bau", "destinoobito_id" );
                    TQuickForm::showField( "form_list_bau", "declaracaoobitomedico_id" );

                } else {

                    $object->dataobito = "";
                    $object->horaobito = "";
                    $object->declaracaoobitodata = "";
                    $object->declaracaoobitohora = "";
                    $object->destinoobito_id = "";
                    $object->declaracaoobitomedico_id = "";

                    TQuickForm::sendData( "form_list_bau", $object );
                    TQuickForm::hideField( "form_list_bau", "dataobito" );
                    TQuickForm::hideField( "form_list_bau", "horaobito" );
                    TQuickForm::hideField( "form_list_bau", "declaracaoobitodata" );
                    TQuickForm::hideField( "form_list_bau", "declaracaoobitohora" );
                    TQuickForm::hideField( "form_list_bau", "destinoobito_id" );
                    TQuickForm::hideField( "form_list_bau", "declaracaoobitomedico_id" );

                }

                break;

        }
    }

    protected $form;
    protected $datagrid;
    protected $pageNavigation;
    protected $formgrid;
    protected $deleteButton;
    protected $transformCallback;

    public function __construct()
    {

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
