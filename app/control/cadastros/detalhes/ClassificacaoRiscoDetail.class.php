<?php

class ClassificacaoRiscoDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    private $framegrid1;
    private $framegrid2;
    private $framegrid3;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "detail_classificacao_risco" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                        = new THidden( "id" );
        $paciente_id               = new THidden( "paciente_id" );
        $bau_id                    = new THidden( "bau_id" );
        $enfermeiro_id             = new THidden( "enfermeiro_id" ); // Deve ser capturado a partir da sessão
        $dataclassificacao         = new TDate( "dataclassificacao" );
        $horaclassificacao         = new TDateTime( "horaclassificacao" );
        $paciente_nome             = new TEntry( "paciente_nome" );
        $pressaoarterial           = new TEntry( "pressaoarterial" );
        $frequenciacardiaca        = new TEntry( "frequenciacardiaca" );
        $frequenciarespiratoria    = new TEntry( "frequenciarespiratoria" );
        $temperatura               = new TEntry( "temperatura" );
        $spo2                      = new TEntry( "spo2" );
        $htg                       = new TEntry( "htg" );
        $observacoes               = new TText( "observacoes" );
        $queixaprincipal           = new TText( "queixaprincipal" );
        $dor                       = new TCombo( "dor" );

        $tipoclassificacaorisco_id = new TDBCombo(
            "tipoclassificacaorisco_id",
            "database", "TipoClassificacaoRiscoRecord",
            "id", "nometipoclassificacaorisco",
            "nometipoclassificacaorisco"
        );

        $tipoestadogeral_id = new TDBCombo(
            "tipoestadogeral_id",
            "database", "TipoEstadoGeralRecord",
            "id", "nomeestadogeral",
            "nomeestadogeral"
        );

        $dor->addItems([
            "LEVE" => "LEVE",
            "MODERADA" => "MODERADA",
            "INTENSA" => "INTENSA"
        ]);

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );
        $page = filter_input( INPUT_GET, "page" );

        try {

            TTransaction::open( "database" );

            $bau = new BauRecord( $fk );
            $paciente = new PacienteRecord( $did );

            if( !empty( $bau ) && !empty( $paciente ) ) {

                $bau_id->setValue( $bau->id );
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );

            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            $action = new TAction( [ "PacientesClassificacaoRiscoList", "onReload" ] );

            new TMessage( "error", "Ocorreu um erro ao carregar as dependência do formulário.", $action );

        }

        switch ( $page ) {

            case "PacientesClassificacaoRiscoList":
                $onReload = new TAction( [ $page, "onReload" ] );
                break;

            case "BauDetail":
                $onReload = new TAction( [ $page, "onReload" ] );
                $onReload->setParameter( "fk", $did );
                break;

        }

        $dataclassificacao        ->setSize("39.4%");
        $horaclassificacao        ->setSize("10%");
        $paciente_nome            ->setSize("49.4%");
        $pressaoarterial          ->setSize("38%");
        $frequenciacardiaca       ->setSize("38%");
        $frequenciarespiratoria   ->setSize("38%");
        $temperatura              ->setSize("38%");
        $spo2                     ->setSize("38%");
        $htg                      ->setSize("38%");
        $dor                      ->setSize("38%");
        $observacoes              ->setSize("78%");
        $queixaprincipal          ->setSize("78%");
        $tipoclassificacaorisco_id->setSize("38%");
        $tipoestadogeral_id       ->setSize("38%");

        $dor                      ->setDefaultOption( "..::SELECIONE::.." );
        $tipoclassificacaorisco_id->setDefaultOption( "..::SELECIONE::.." );
        $tipoestadogeral_id       ->setDefaultOption( "..::SELECIONE::.." );

        $horaclassificacao->setMask( "hh:ii" );
        $dataclassificacao->setMask( "dd/mm/yyyy" );
        $dataclassificacao->setDatabaseMask( "yyyy-mm-dd" );

        $horaclassificacao->setValue( date( "H:i" ) );
        $dataclassificacao->setValue( date( "d/m/Y" ) );

        $paciente_nome->setEditable( false );
        $dataclassificacao->setEditable( false );
        $horaclassificacao->setEditable( false );

        $paciente_nome->forceUpperCase();

        $paciente_id      ->addValidation( TextFormat::set( "Nome do Paciente" ), new TRequiredValidator );
        $dataclassificacao->addValidation( TextFormat::set( "Data da Avaliação" ), new TRequiredValidator );

        $page2 = new TLabel( "Histórico Patológico", "#7D78B6", 12, "bi");
        $page2->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Histórico Patológico" );
        $this->form->addContent( [ $page2 ] );

        /*-------------------------- frame de comorbidades -------------------*/
        $frame1 = new TFrame;
        $frame1->setLegend( "Comorbidades" );
        $frame1->style .= ';margin:0px;width:95%';
        $cid_id = new TDBCombo ( 'cid_id', 'database', 'VwCidRecord', 'id', 'nomecid', 'nomecid' );
        $cid_id->{'placeholder'} = "DIGITE O NOME OU CÓDIGO DO CID";
        $cid_id->{'style'} = "text-transform:uppercase;width:100%;";
        $cid_id->enableSearch();
        $add_button1 = TButton::create(
            "add1", [ $this,"throwbackToPage" ], null, null
        );
        $onSaveFrame1 = new TAction( [ $this, "onSaveFrames" ] );
        $onSaveFrame1->setParameter( "fk", $fk );
        $onSaveFrame1->setParameter( "did", $did );
        $onSaveFrame1->setParameter( "page", $page );
        $onSaveFrame1->setParameter( "frm", 1 );
        $add_button1->setAction( $onSaveFrame1 );
        $add_button1->setLabel( "Adicionar" );
        $add_button1->setImage( "fa:plus green" );
        $this->form->addContent( [ $frame1 ] );
        $this->form->addField( $cid_id );
        $this->form->addField( $add_button1 );
        $this->framegrid1 = new TQuickGrid();
        $this->framegrid1->makeScrollable();
        $this->framegrid1->style='width:100%;height:0%;';
        $this->framegrid1->id = 'framegrid1';
        $this->framegrid1->disableDefaultClick();
        $remove_action1 = new TDataGridAction( [ $this, "onDeleteFrames" ] );
        $remove_action1->setParameter( "fk", $fk );
        $remove_action1->setParameter( "did", $did );
        $remove_action1->setParameter( "page", $page );
        $remove_action1->setParameter( "frm", 1 );
        $this->framegrid1->addQuickAction( "Remover", $remove_action1, "id", "fa:trash red", "20%" );
        $this->framegrid1->addQuickColumn( "Patologia", 'cid_codnome', 'left', '100%');
        $this->framegrid1->createModel();
        $hbox1 = new THBox;
        $hbox1->add( $cid_id );
        $hbox1->add( $add_button1 );
        $hbox1->style = 'margin:10px';
        $vbox1 = new TVBox;
        $vbox1->style = 'width:100%';
        $vbox1->add( $hbox1 );
        $vbox1->add( $this->framegrid1 );
        $frame1->add( $vbox1 );
        /*--------------------------------------------------------------------*/

        /*--------------------- frame de uso de medicacoes -------------------*/
        $frame2 = new TFrame;
        $frame2->setLegend( "Uso de Medicações" );
        $frame2->style .= ';margin:0px;width:95%';
        $medicamento_id = new TDBCombo ( 'medicamento_id', 'database', 'MedicamentoRecord', 'id', 'nomemedicamento', 'nomemedicamento' );
        $medicamento_id->{'placeholder'} = "DIGITE O NOME OU PRINCÍPIO DO MEDICAMENTO";
        $medicamento_id->{'style'} = "text-transform:uppercase;width:100%;";
        $medicamento_id->enableSearch();
        $add_button2 = TButton::create(
            "add2", [ $this,"throwbackToPage" ], null, null
        );
        $onSaveFrame2 = new TAction( [ $this, "onSaveFrames" ] );
        $onSaveFrame2->setParameter( "fk", $fk );
        $onSaveFrame2->setParameter( "did", $did );
        $onSaveFrame2->setParameter( "page", $page );
        $onSaveFrame2->setParameter( "frm", 2 );
        $add_button2->setAction( $onSaveFrame2 );
        $add_button2->setLabel( "Adicionar" );
        $add_button2->setImage( "fa:plus green" );
        $this->form->addContent( [ $frame2 ] );
        $this->form->addField( $medicamento_id );
        $this->form->addField( $add_button2 );
        $this->framegrid2 = new TQuickGrid();
        $this->framegrid2->makeScrollable();
        $this->framegrid2->style='width:100%;height:0%;';
        $this->framegrid2->id = 'framegrid2';
        $this->framegrid2->disableDefaultClick();
        $remove_action2 = new TDataGridAction( [ $this, "onDeleteFrames" ] );
        $remove_action2->setParameter( "fk", $fk );
        $remove_action2->setParameter( "did", $did );
        $remove_action2->setParameter( "page", $page );
        $remove_action2->setParameter( "frm", 2 );
        $this->framegrid2->addQuickAction( "Remover", $remove_action2, "id", "fa:trash red", "20%" );
        $this->framegrid2->addQuickColumn( "Medicamento", 'medicamento_nome', 'left', '100%');
        $this->framegrid2->createModel();
        $hbox2 = new THBox;
        $hbox2->add( $medicamento_id );
        $hbox2->add( $add_button2 );
        $hbox2->style = 'margin:4px';
        $vbox2 = new TVBox;
        $vbox2->style = 'width:100%';
        $vbox2->add( $hbox2 );
        $vbox2->add( $this->framegrid2 );
        $frame2->add( $vbox2 );
        /*--------------------------------------------------------------------*/

        /*----------------------- frame de alergia medicamentosa -------------*/
        $frame3 = new TFrame;
        $frame3->setLegend( "Alergia Medicamentosa" );
        $frame3->style .= ';margin:0px;width:95%';
        $principioativo_id = new TDBCombo ( 'principioativo_id', 'database', 'PrincipioAtivoRecord', 'id', 'nomeprincipioativo', 'nomeprincipioativo' );
        $principioativo_id->{'placeholder'} = "DIGITE O NOME OU PRINCÍPIO DO MEDICAMENTO";
        $principioativo_id->{'style'} = "text-transform:uppercase;width:100%;";
        $principioativo_id->enableSearch();
        $add_button3 = TButton::create(
            "add3", [ $this,"throwbackToPage" ], null, null
        );
        $onSaveFrame3 = new TAction( [ $this, "onSaveFrames" ] );
        $onSaveFrame3->setParameter( "fk", $fk );
        $onSaveFrame3->setParameter( "did", $did );
        $onSaveFrame3->setParameter( "page", $page );
        $onSaveFrame3->setParameter( "frm", 3 );
        $add_button3->setAction( $onSaveFrame3 );
        $add_button3->setLabel( "Adicionar" );
        $add_button3->setImage( "fa:plus green" );
        $this->form->addContent( [ $frame3 ] );
        $this->form->addField( $principioativo_id );
        $this->form->addField( $add_button3 );
        $this->framegrid3 = new TQuickGrid();
        $this->framegrid3->makeScrollable();
        $this->framegrid3->style='width:100%;height:0%;';
        $this->framegrid3->id = 'framegrid3';
        $this->framegrid3->disableDefaultClick();
        $remove_action3 = new TDataGridAction( [ $this, "onDeleteFrames" ] );
        $remove_action3->setParameter( "fk", $fk );
        $remove_action3->setParameter( "did", $did );
        $remove_action3->setParameter( "page", $page );
        $remove_action3->setParameter( "frm", 3 );
        $this->framegrid3->addQuickAction( "Remover", $remove_action3, "id", "fa:trash red", "20%" );
        $this->framegrid3->addQuickColumn( "Principio Ativo", 'principioativo_nome', 'left', '100%');
        $this->framegrid3->createModel();
        $hbox3 = new THBox;
        $hbox3->add( $principioativo_id );
        $hbox3->add( $add_button3 );
        $hbox3->style = 'margin:4px';
        $vbox3 = new TVBox;
        $vbox3->style = 'width:100%';
        $vbox3->add( $hbox3 );
        $vbox3->add( $this->framegrid3 );
        $frame3->add( $vbox3 );
        /*--------------------------------------------------------------------*/

        $page1 = new TLabel( "Sinais Vitais", "#7D78B6", 12, "bi");
        $page1->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Sinais Vitais" );
        $this->form->addContent( [ $page1 ] );
        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data da Avaliação: {$redstar}" ) ], [ $dataclassificacao, $horaclassificacao ] );
        $this->form->addFields( [ new TLabel( "Pressão Arterial:" ) ], [ $pressaoarterial ] );
        $this->form->addFields( [ new TLabel( "Frequência Cardíaca:" ) ], [ $frequenciacardiaca ] );
        $this->form->addFields( [ new TLabel( "Frequência Respiratória:" ) ], [ $frequenciarespiratoria ] );
        $this->form->addFields( [ new TLabel( "Temperatura:" ) ], [ $temperatura ] );
        $this->form->addFields( [ new TLabel( "SPO2:" ) ], [ $spo2 ] );
        $this->form->addFields( [ new TLabel( "HTG:" ) ], [ $htg ] );

        $page3 = new TLabel( "Estado Geral", "#7D78B6", 12, "bi");
        $page3->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Estado Geral" );
        $this->form->addContent( [ $page3 ] );
        $this->form->addFields( [ new TLabel( "Escala de Dor:" ) ], [ $dor ] );
        $this->form->addFields( [ new TLabel( "Estado Geral:" ) ], [ $tipoestadogeral_id ] );
        $this->form->addFields( [ new TLabel( "Queixa Principal:" ) ], [ $queixaprincipal ] );
        $this->form->addFields( [ new TLabel( "Observações:" ) ], [ $observacoes ] );

        /*---------------- frame dos botoes de classificacao -----------------*/
        $frame4 = new TFrame;
        $hbox4  = new THBox;
        $vbox4  = new TVBox;
        $frame4->setLegend( "Classificação" );
        $frame4->style = 'margin: 0 auto 0 17%; width: 62%;';
        $hbox4->style = 'margin:4px;';
        $vbox4->style = 'width:100%';
        $this->form->addContent( [ $frame4 ] );
        $tipoclassificacaorisco_id = new THidden( "tipoclassificacaorisco_id" );
        $button_style = "width:122px; color:#FFF !important; font-weight: bold;";
        $save_parameters = [ "fk" => $fk, "did" => $did, "page" => $page ];

        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "TipoClassificacaoRiscoRecord" );

            $criteria = new TCriteria();
            $criteria->setProperty( "order", "ordem" );

            $objects = $repository->load( $criteria );

            if ( !empty( $objects ) ) {

                foreach ( $objects as $object ) {

                    $onSave = new TAction( [ $this, "onSave" ] );
                    $onSave->setParameters( $save_parameters );
                    $onSave->setParameter( "priority", $object->ordem );

                    $add_button = TButton::create( "priority{$object->ordem}", [ $this,"throwbackToPage" ], null, null );
                    $add_button->setAction( $onSave );
                    $add_button->setLabel( $object->nometipoclassificacaorisco );
                    $add_button->style = "background-color:{$object->cortipoclassificacaorisco};{$button_style}";

                    $this->form->addField( $add_button );
                    $hbox4->add( $add_button );

                }

            }

            TTransaction::close();

        } catch (Exception $e) {

            $this->throwbackToPage();

        }

        $vbox4->add( $hbox4 );
        $frame4->add( $vbox4 );
        /*--------------------------------------------------------------------*/

        $this->form->addFields( [ $id, $paciente_id, $bau_id, $enfermeiro_id ] );

        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_paciente_nome               = new TDataGridColumn( "paciente_nome", "Paciente", "left" );
        $column_dataclassificacao           = new TDataGridColumn( "dataclassificacao", "Data da Avaliação", "left" );
        $column_horaclassificacao           = new TDataGridColumn( "horaclassificacao", "Hora da Avaliação", "left" );
        $column_queixaprincipal             = new TDataGridColumn( "queixaprincipal", "Queixa Principal", "left" );
        $column_tipoclassificacaorisco_nome = new TDataGridColumn( "tipoclassificacaorisco_nome", "Classificação", "left" );
        $column_enfermeiro_nome             = new TDataGridColumn( "enfermeiro_nome", "Enfermeiro", "left" );

        $this->datagrid->addColumn( $column_paciente_nome );
        $this->datagrid->addColumn( $column_dataclassificacao );
        $this->datagrid->addColumn( $column_horaclassificacao );
        $this->datagrid->addColumn( $column_queixaprincipal );
        $this->datagrid->addColumn( $column_tipoclassificacaorisco_nome );
        $this->datagrid->addColumn( $column_enfermeiro_nome );

        $action_edit = new CustomDataGridAction( [ $this, "onEdit" ] );
        $action_edit->setButtonClass( "btn btn-default" );
        $action_edit->setLabel( "Editar" );
        $action_edit->setImage( "fa:pencil-square-o blue fa-lg" );
        $action_edit->setField( "id" );
        $action_edit->setParameter( "fk", $fk );
        $action_edit->setParameter( "did", $did );
        $action_edit->setParameter( "page", $page );
        $this->datagrid->addAction( $action_edit );

        $action_del = new CustomDataGridAction( [ $this, "onDelete" ] );
        $action_del->setButtonClass( "btn btn-default" );
        $action_del->setLabel( "Deletar" );
        $action_del->setImage( "fa:trash-o red fa-lg" );
        $action_del->setField( "id" );
        $action_del->setParameter( "fk", $fk );
        $action_del->setParameter( "did", $did );
        $action_del->setParameter( "page", $page );
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

    public function onSave( $param = null )
    {
        try {

            $this->form->validate();

            $object = $this->form->getData( "ClassificacaoRiscoRecord" );

            TTransaction::open( "database" );

            unset( $object->cid_id );
            unset( $object->medicamento_id );
            unset( $object->principioativo_id );
            unset( $object->paciente_nome );

            if ( isset( $param[ "priority" ] ) ) {

                $criteria = new TCriteria();
                $criteria->add( new TFilter( "ordem", "=", $param[ "priority" ] ) );
                $rows = TipoClassificacaoRiscoRecord::getObjects( $criteria );

                foreach ( $rows as $row ) {
                    $object->tipoclassificacaorisco_id = $row->id;
                    break;
                }

            }

            $object->store();

            TTransaction::close();

            new TMessage( "info", "Registro salvo com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }

        $this->onReload( $param );
    }

    public function onEdit( $param = null )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new ClassificacaoRiscoRecord( $param[ "key" ] );

                $dataclassificacao = new DateTime( $object->dataclassificacao );
                $horaclassificacao = new DateTime( $object->horaclassificacao );

                $object->dataclassificacao = $dataclassificacao->format("d/m/Y");
                $object->horaclassificacao = $horaclassificacao->format("H:i");

                $this->form->setData( $object );

                TTransaction::close();

            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }

        $this->onReload( $param );
    }

    public function onDelete( $param = null )
    {
        if( isset( $param[ "key" ] ) ) {

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

            $object = new ClassificacaoRiscoRecord( $param[ "key" ] );

            $object->delete();

            TTransaction::close();

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }

        $this->onReload( $param );
    }

    public function onReload( $param = null )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "ClassificacaoRiscoRecord" );

            $properties = [
                "order" => "dataclassificacao",
                "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bau_id", "=", $param[ "fk" ] ) );
            $criteria->add( new TFilter( "paciente_id", "=", $param[ "did" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( !empty( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    $dataclassificacao = new DateTime( $object->dataclassificacao );
                    $horaclassificacao = new DateTime( $object->horaclassificacao );

                    $object->dataclassificacao = $dataclassificacao->format("d/m/Y");
                    $object->horaclassificacao = $horaclassificacao->format("H:i");

                    $this->datagrid->addItem( $object );

                }

            }

            $criteria->resetProperties();

            $count = $repository->count( $criteria );

            $this->pageNavigation->setCount( $count );
            $this->pageNavigation->setProperties( $properties );
            $this->pageNavigation->setLimit( $limit );

            $this->onReloadFrames( $param );

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

    public function onSaveFrames( $param = null )
    {
        try {

            $object = $this->unSetFields( $param );

            TTransaction::open( "database" );

            if ( !empty( $object ) ) {
                $object->store();
            } else {
                $this->throwbackToPage();
            }

            TTransaction::close();


        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }

        $this->onReload( $param );
    }

    public function onDeleteFrames( $param = null )
    {
        try {

            TTransaction::open( "database" );

            $object = $this->getFrameItem( $param );

            if ( !empty( $object ) ) {
                $object->delete();
            } else {
                $this->throwbackToPage();
            }

            TTransaction::close();


        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }

        $this->onReload( $param );
    }

    public function onReloadFrames( $param = null )
    {
        try {

            TTransaction::open('database');

            $object = new PacienteRecord( $param[ "did" ] );

            if ( !empty( $object ) ) {

                foreach ( $object->getComorbidades() as $comorbidade ) {
                    $this->framegrid1->addItem( $comorbidade );
                }

                foreach ( $object->getMedicacoes() as $medicacao ) {
                    $this->framegrid2->addItem( $medicacao );
                }

                foreach ( $object->getAlergias() as $alergia ) {
                    $this->framegrid3->addItem( $alergia );
                }

            }

            TTransaction::close();

        } catch( Exception $ex ) {

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function unSetFields( $param = null )
    {
        switch ( $param[ "frm" ] ) {

            case 1:

                $object = $this->form->getData( "BauComorbidadesRecord" );
                unset( $object->medicamento_id );
                unset( $object->principioativo_id );

                break;

            case 2:

                $object = $this->form->getData( "BauUsoMedicacoesRecord" );
                unset( $object->cid_id );
                unset( $object->principioativo_id );

                break;

            case 3:

                $object = $this->form->getData( "BauAlergiaMedicamentosaRecord" );
                unset( $object->cid_id );
                unset( $object->medicamento_id );

                break;

        }

        if ( !empty( $object ) ) {

            unset( $object->id );
            unset( $object->enfermeiro_id );
            unset( $object->tipoclassificacaorisco_id );
            unset( $object->tipoestadogeral_id );
            unset( $object->paciente_nome );
            unset( $object->dataclassificacao );
            unset( $object->horaclassificacao );
            unset( $object->pressaoarterial );
            unset( $object->frequenciacardiaca );
            unset( $object->frequenciarespiratoria );
            unset( $object->temperatura );
            unset( $object->spo2 );
            unset( $object->htg );
            unset( $object->queixaprincipal );
            unset( $object->dor );
            unset( $object->observacoes );

            return $object;

        } else {

            return null;

        }

    }

    public function getFrameItem( $param = null )
    {
        if ( isset( $param[ "frm" ] ) ) {

            switch ( $param[ "frm" ] ) {

                case 1:
                    $object = new BauComorbidadesRecord( $param[ "key" ] );
                    break;

                case 2:
                    $object = new BauUsoMedicacoesRecord( $param[ "key" ] );
                    break;

                case 3:
                    $object = new BauAlergiaMedicamentosaRecord( $param[ "key" ] );
                    break;

            }

        }

        return !empty( $object ) ? $object : null;
    }

    public function throwbackToPage()
    {
        $action = new TAction( [ "PacientesClassificacaoRiscoList", "onReload" ] );

        new TMessage( "error", "Uma instabilidade momentâneo no sistema impediu a ação, tente novamente mais tarde.", $action );
    }
}
