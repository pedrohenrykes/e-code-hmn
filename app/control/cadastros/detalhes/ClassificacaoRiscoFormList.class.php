<?php

class ClassificacaoRiscoFormList extends TPage
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

        $this->form = new BootstrapFormBuilder( "form_list_classificacao_risco" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id                        = new THidden( "id" );
        $paciente_id               = new THidden( "paciente_id" );
        $bau_id                    = new THidden( "bau_id" );
        $enfermeiro_id             = new THidden( "enfermeiro_id" ); // Deve ser capturado a partir da sessão
        $dataclassificacao         = new TDate( "dataclassificacao" );
        $horaclassificacao         = new TDateTime( "horaclassificacao" );
        $paciente_nome             = new TEntry( "paciente_name" );
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

        try {

            TTransaction::open( "database" );

            $bau = new BauRecord( $fk );
            $paciente = new PacienteRecord( $did );

            if( isset( $bau ) && isset( $paciente ) ) {

                $bau_id->setValue( $bau->id );
                $paciente_id->setValue( $paciente->id );
                $paciente_nome->setValue( $paciente->nomepaciente );

            }

            TTransaction::close();

        } catch ( Exception $ex ) {

            $action = new TAction( [ "PacientesEmClassificacaoList", "onReload" ] );

            new TMessage( "error", "Ocorreu um erro ao carregar as dependência do formulário.", $action );

        }

        $dataclassificacao        ->setSize("38%");
        $horaclassificacao        ->setSize("38%");
        $paciente_nome            ->setSize("38%");
        $pressaoarterial          ->setSize("38%");
        $frequenciacardiaca       ->setSize("38%");
        $frequenciarespiratoria   ->setSize("38%");
        $temperatura              ->setSize("38%");
        $spo2                     ->setSize("38%");
        $htg                      ->setSize("38%");
        $dor                      ->setSize("38%");
        $observacoes              ->setSize("38%");
        $queixaprincipal          ->setSize("38%");
        $tipoclassificacaorisco_id->setSize("38%");
        $tipoestadogeral_id       ->setSize("38%");

        $dor                      ->setDefaultOption( "..::SELECIONE::.." );
        $tipoclassificacaorisco_id->setDefaultOption( "..::SELECIONE::.." );
        $tipoestadogeral_id       ->setDefaultOption( "..::SELECIONE::.." );

        $dataclassificacao->setMask( "dd/mm/yyyy" );
        $dataclassificacao->setDatabaseMask("yyyy-mm-dd");
        $horaclassificacao->setMask( "hh:ii" );

        $dataclassificacao->setValue( date( "d/m/Y" ) );
        $horaclassificacao->setValue( date( "H:i" ) );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $paciente_id      ->addValidation( TextFormat::set( "Nome do Paciente" ), new TRequiredValidator );
        $dataclassificacao->addValidation( TextFormat::set( "Data da Avaliação" ), new TRequiredValidator );

        $page1 = new TLabel( "Sinais Vitais", "#7D78B6", 12, "bi");
        $page1->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Sinais Vitais" );
        $this->form->addContent( [ $page1 ] );
        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data da Avaliação: {$redstar}" ) ], [ $dataclassificacao ] );
        $this->form->addFields( [ new TLabel( "Hora da Avaliação:" ) ], [ $horaclassificacao ] );
        $this->form->addFields( [ new TLabel( "Pressão Arterial:" ) ], [ $pressaoarterial ] );
        $this->form->addFields( [ new TLabel( "Frequência Cardíaca:" ) ], [ $frequenciacardiaca ] );
        $this->form->addFields( [ new TLabel( "Frequência Respiratória:" ) ], [ $frequenciarespiratoria ] );
        $this->form->addFields( [ new TLabel( "Temperatura:" ) ], [ $temperatura ] );
        $this->form->addFields( [ new TLabel( "SPO2:" ) ], [ $spo2 ] );
        $this->form->addFields( [ new TLabel( "HTG:" ) ], [ $htg ] );

        $page2 = new TLabel( "Histórico Patológico", "#7D78B6", 12, "bi");
        $page2->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Histórico Patológico" );
        $this->form->addContent( [ $page2 ] );

        $add_button1 = TButton::create( "add1", [ $this,"onAddItem" ], "Adicionar", 'fa:plus green');
        $add_button2 = TButton::create( "add2", [ $this,"onAddItem" ], "Adicionar", 'fa:plus green');
        $add_button3 = TButton::create( "add3", [ $this,"onAddItem" ], "Adicionar", 'fa:plus green');

        // TODO concluir os frames
        /*--- frame de comorbidades ---*/
        $frame1 = new TFrame;
        $frame1->setLegend( "Comorbidades" );
        $frame1->style .= ';margin:0px;width:95%';
        $this->form->addContent( [ $frame1 ] );
        $this->form->addField( $add_button1 );
        $this->framegrid1 = new TQuickGrid();
        $this->framegrid1->setHeight(200);
        $this->framegrid1->makeScrollable();
        $this->framegrid1->style='width: 100%';
        $this->framegrid1->id = 'framegrid1';
        $this->framegrid1->disableDefaultClick();
        $this->framegrid1->addQuickColumn( '', 'delete', 'center', '5%');
        $this->framegrid1->addQuickColumn( "Patologia", 'name', 'left', '85%');
        $this->framegrid1->createModel();
        $hbox1 = new THBox;
        $hbox1->add( $add_button1 );
        $hbox1->style = 'margin: 4px';
        $vbox1 = new TVBox;
        $vbox1->style='width:100%';
        $vbox1->add( $hbox1 );
        $vbox1->add( $this->framegrid1 );
        $frame1->add( $vbox1 );
        /*--------------------------------------*/

        /*--- frame de uso de medicacoes ---*/
        $frame2 = new TFrame;
        $frame2->setLegend( "Uso de Medicações" );
        $frame2->style .= ';margin:0px;width:95%';
        $this->form->addContent( [ $frame2 ] );
        $this->form->addField( $add_button2 );
        $this->framegrid2 = new TQuickGrid();
        $this->framegrid2->setHeight(200);
        $this->framegrid2->makeScrollable();
        $this->framegrid2->style='width: 100%';
        $this->framegrid2->id = 'framegrid2';
        $this->framegrid2->disableDefaultClick();
        $this->framegrid2->addQuickColumn( '', 'delete', 'center', '5%');
        $this->framegrid2->addQuickColumn( "Principio Ativo", 'name', 'left', '85%');
        $this->framegrid2->createModel();
        $hbox2 = new THBox;
        $hbox2->add( $add_button2 );
        $hbox2->style = 'margin: 4px';
        $vbox2 = new TVBox;
        $vbox2->style='width:100%';
        $vbox2->add( $hbox2 );
        $vbox2->add( $this->framegrid2 );
        $frame2->add( $vbox2 );
        /*--------------------------------------*/

        /*--- frame de alergia medicamentosa ---*/
        $frame3 = new TFrame;
        $frame3->setLegend( "Alergia Medicamentosa" );
        $frame3->style .= ';margin:0px;width:95%';
        $this->form->addContent( [ $frame3 ] );
        $this->form->addField( $add_button3 );
        $this->framegrid3 = new TQuickGrid();
        $this->framegrid3->setHeight(200);
        $this->framegrid3->makeScrollable();
        $this->framegrid3->style='width: 100%';
        $this->framegrid3->id = 'framegrid3';
        $this->framegrid3->disableDefaultClick();
        $this->framegrid3->addQuickColumn( '', 'delete', 'center', '5%');
        $this->framegrid3->addQuickColumn( "Principio Ativo", 'name', 'left', '85%');
        $this->framegrid3->createModel();
        $hbox3 = new THBox;
        $hbox3->add( $add_button3 );
        $hbox3->style = 'margin: 4px';
        $vbox3 = new TVBox;
        $vbox3->style='width:100%';
        $vbox3->add( $hbox3 );
        $vbox3->add( $this->framegrid3 );
        $frame3->add( $vbox3 );
        /*--------------------------------------*/

        $page3 = new TLabel( "Estado Geral", "#7D78B6", 12, "bi");
        $page3->style="text-align:left;border-bottom:1px solid #c0c0c0;width:100%";
        $this->form->appendPage( "Estado Geral" );
        $this->form->addContent( [ $page3 ] );
        $this->form->addFields( [ new TLabel( "Escala de Dor:" ) ], [ $dor ] );
        $this->form->addFields( [ new TLabel( "Estado Geral:" ) ], [ $tipoestadogeral_id ] );
        $this->form->addFields( [ new TLabel( "Queixa Principal:" ) ], [ $queixaprincipal ] );
        $this->form->addFields( [ new TLabel( "Observações:" ) ], [ $observacoes ] );
        $this->form->addFields( [ new TLabel( "Classificação: {$redstar}" ) ], [ $tipoclassificacaorisco_id ] );
        $this->form->addFields( [ $id, $paciente_id, $bau_id, $enfermeiro_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );

        $onReload = new TAction( [ "BauFormList", "onReload" ] );
        $onReload->setParameter( "fk", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar para B.A.U.", $onReload, "fa:table blue" );

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
        $container->style = "width: 90%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "ClassificacaoRiscoRecord" );

        try {

            $this->form->validate();

            TTransaction::open( "database" );

            unset( $object->paciente_name );

            $object->bau_id = $param[ "fk" ];

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "ClassificacaoRiscoFormList", "onReload" ] );
            $action->setParameters( $param );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            $this->form->setData( $object );

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
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

                $data = [];
                foreach ( $object->getComorbidades() as $comorbidade ) {

                    $data[ $comorbidade->id ] = $comorbidade->toArray();

                    $item = new stdClass;
                    $item->id = $comorbidade->id;
                    $item->name = $comorbidade->name;

                    $i = new TElement('i');
                    $i->{'class'} = 'fa fa-trash red';
                    $btn = new TElement('a');
                    $btn->{'onclick'} = "__adianti_ajax_exec('class=SystemGroupForm&method=deleteProgram&id={$comorbidade->id}');$(this).closest('tr').remove();";
                    $btn->{'class'} = 'btn btn-default btn-sm';
                    $btn->add( $i );

                    $item->delete = $btn;
                    $tr = $this->program_list->addItem($item);
                    $tr->{'style'} = 'width: 100%;display: inline-table;';
                }

                $this->onReload( $param );

                $this->form->setData( $object );

                TTransaction::close();

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
                "fk"  => $param[ "fk" ],
                "did"  => $param[ "did" ]
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

            $object = new ClassificacaoRiscoRecord( $param[ "key" ] );

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

            if ( isset( $objects ) ) {

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

            TTransaction::close();

            $this->loaded = true;

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );
        }
    }

    public static function onAddItem( $param = null )
    {


        $window = TWindow::create( "Adição de Comorbidado", 0.600, 0.800 );
        $window->add(  );
        $window->show();
    }

    public function onClear()
    {
        $this->form->clear();
    }
}
