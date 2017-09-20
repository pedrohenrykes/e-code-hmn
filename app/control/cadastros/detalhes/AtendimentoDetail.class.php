<?php

class AtendimentoDetail extends TPage
{
    private $form;
    private $datagrid;
    private $pageNavigation;
    private $loaded;

    public function __construct()
    {
        parent::__construct();

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_list_atendimento" );
        $this->form->setFormTitle( "Registro de Atendimento" );
        $this->form->class = "tform";

        $id                     = new THidden( "id" );
        $paciente_id            = new THidden( "paciente_id" );
        $bau_id                 = new THidden( "bau_id" );
        $profissional_id        = new THidden( "profissional_id" ); // Deve ser capturado a partir da sessão
        $paciente_nome          = new TEntry( "paciente_nome" );
        $dataclassificacao      = new TDate( "dataatendimento" );
        $horaclassificacao      = new TDateTime( "horaatendimento" );
        $exameclinico           = new TText( "exameclinico" );
        $examescomplementares   = new TText( "examescomplementares" );
        //$diagnosticomedico      = new TText( "diagnosticomedico" );
        $descricaotratamento    = new TText( "descricaotratamento" );

        $paciente_nome->setSize("60%");
        $exameclinico->setSize("90%");
        $examescomplementares->setSize("90%");
        $descricaotratamento->setSize("90%");
        $horaclassificacao->setSize("15%");
        $dataclassificacao->setSize("45%");

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bau_id->setValue ($fk);
        $paciente_id->setValue ($did);
        $profissional_id->setValue ($did);
        //$profissional_id  ALTERAR PARA O DA SESSSAAAAOOOOO

        try {
            TTransaction::open( "database" );
            $paciente = new PacienteRecord($did);
            if( isset($paciente) ) {
                $paciente_nome->setValue($paciente->nomepaciente);
            }
            TTransaction::close();
        } catch (Exception $ex) {
            $action = new TAction(["PacientesEncaminhamentoList", "onReload"]);
            new TMessage("error", "Ocorreu um erro ao carregar as dependência do formulário.", $action);
        }


        $dataclassificacao->setMask( "dd/mm/yyyy" );
        $dataclassificacao->setDatabaseMask("yyyy-mm-dd");
        $horaclassificacao->setMask( "hh:ii" );

        $dataclassificacao->setValue( date( "d/m/Y" ) );
        $horaclassificacao->setValue( date( "H:i" ) );
        $horaclassificacao->setEditable( false );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $dataclassificacao->addValidation( TextFormat::set( "Data da Avaliação" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields([ new TLabel( "Data do Atendimento: {$redstar}" ) ], [ $dataclassificacao , $horaclassificacao ] );

        $this->form->addFields( [ new TLabel( "Exame Clinico:" ) ], [ $exameclinico ] );

        $this->form->addFields( [ new TLabel( "Exames Complementares:" ) ], [ $examescomplementares ] );
        //$this->form->addFields( [ new TLabel( "Diagnóstico:" ) ], [ $diagnosticomedico ] );
        $this->form->addFields( [ new TLabel( "Descrição do Tratamento:" ) ], [ $descricaotratamento ] );

        /*--- frame de comorbidades ---*/
        $frame1 = new TFrame;
        $frame1->setLegend( "Diagnóstico" );
        $frame1->style .= ';margin:0%;width:90%';
        $cid_id     = new THidden( "cid_id" );
        $cid_codigo = new TDBSeekButton(
            "cid_codigo", "database", "form_list_atendimento",
            "VwCidRecord", "nomecid", "cid_id", "cid_codigo"
        );
        $add_button1 = TButton::create(
            "add1", [ $this,"onError" ], null, null
        );
        $onSaveFrame1 = new TAction( [ $this, "onSaveFrames" ] );
        $onSaveFrame1->setParameter( "fk", $fk );
        $onSaveFrame1->setParameter( "did", $did );
        $onSaveFrame1->setParameter( "frm", 1 );
        $add_button1->setAction( $onSaveFrame1 );
        $add_button1->setLabel( "Adicionar" );
        $add_button1->setImage( "fa:plus green" );
        $this->form->addContent( [ $frame1 ] );
        $this->form->addField( $cid_codigo );
        $this->form->addField( $add_button1 );
        $this->framegrid1 = new TQuickGrid();
        $this->framegrid1->setHeight('0%');
        $this->framegrid1->makeScrollable();
        $this->framegrid1->style='width: 100%';
        $this->framegrid1->id = 'framegrid1';
        $this->framegrid1->disableDefaultClick();
        $remove_action1 = new TDataGridAction( [ $this, "onDeleteFrames" ] );
        $remove_action1->setParameter( "fk", $fk );
        $remove_action1->setParameter( "did", $did );
        $remove_action1->setParameter( "frm", 1 );
        $this->framegrid1->addQuickAction( "Remover", $remove_action1, "id", "fa:trash red", "0%" );
        $this->framegrid1->addQuickColumn( "Patologia", 'cid_codnome', 'left', '100%');
        $this->framegrid1->createModel();
        $hbox1 = new THBox;
        $hbox1->add( $cid_codigo );
        $hbox1->add( $add_button1 );
        $hbox1->style = 'margin: 0%';
        $vbox1 = new TVBox;
        $vbox1->style='width:100%';
        $vbox1->add( $hbox1 );
        $vbox1->add( $this->framegrid1 );
        $frame1->add( $vbox1 );
        /*--------------------------------------*/

        /*--- frame de comorbidades ---*/
        $frame2 = new TFrame;
        $frame2->setLegend( "Direcionamento" );
        $frame2->style .= ';margin:0%;width:90%';

        $add_button2 = TButton::create("buttonmed", [ $this,"onError" ], null, null);
        $onSaveFrame2 = new TAction( [ 'PrescreverMedicacaoDetail', "onReload" ] );
        $onSaveFrame2->setParameter( "fk", $fk );
        $onSaveFrame2->setParameter( "did", $did );
        $onSaveFrame2->setParameter( "frm", 1 );
        $add_button2->setAction( $onSaveFrame2 );
        $add_button2->setLabel( "Prescrever Medicação" );
        $add_button2->setImage( "fa:plus green" );

        $add_button3 = TButton::create("buttonalt", [ $this,"onError" ], null, null);
        $onSaveFrame3 = new TAction( [ 'PacientesAltaHospitalarList', "onReload" ] );
        $onSaveFrame3->setParameter( "fk", $fk );
        $onSaveFrame3->setParameter( "did", $did );
        $onSaveFrame3->setParameter( "frm", 1 );
        $add_button3->setAction( $onSaveFrame3 );
        $add_button3->setLabel( "Alta Hospitalar" );
        $add_button3->setImage( "fa:plus green" );

        $this->form->addField( $add_button2 );
        $this->form->addField( $add_button3 );

        $this->form->addContent( [ $frame2 ] );
        $hbox2 = new THBox;
        $hbox2->add( $add_button2 );
        $hbox2->add( $add_button3 );
        $hbox2->style = 'margin: 0%';
        $vbox2 = new TVBox;
        $vbox2->style='width:100%';
        $vbox2->add( $hbox2 );
        $frame2->add( $vbox2 );
        /*--------------------------------------*/
        $this->form->addFields( [ $id, $bau_id, $paciente_id, $profissional_id, $cid_id ] );

        $onSave = new TAction( [ $this, "onSave" ] );
        $onSave->setParameter( "fk", $fk );
        $onSave->setParameter( "did", $did );

        $onReload = new TAction( [ "PacientesAtendimentoList", "onReload" ] );
        $onReload->setParameter( "did", $did );

        $this->form->addAction( "Salvar", $onSave, "fa:floppy-o" );
        $this->form->addAction( "Voltar", $onReload, "fa:table blue" );

        $this->datagrid = new BootstrapDatagridWrapper( new CustomDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( '100%' );

        $column_1 = new TDataGridColumn( "paciente_nome", "Paciente", "left" );
        $column_2 = new TDataGridColumn( "enfermeiro_nome", "Responsável", "left" );
        $column_3 = new TDataGridColumn( "dataatendimento", "Data", "left" );
        $column_4 = new TDataGridColumn( "horaatendimento", "Hora", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );
        $this->datagrid->addColumn( $column_4 );

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

    public function onSave( $param = null )
    {
        $object = $this->form->getData( "BauAtendimentoRecord" );

        try {

            $this->form->validate();
            TTransaction::open( "database" );
            unset( $object->paciente_nome );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "AtendimentoDetail", "onReload" ] );
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

                $object = new BauAtendimentoRecord( $param[ "key" ] );

                //$dataclassificacao = new DateTime( $object->dataclassificacao );
                //$object->dataclassificacao = $dataclassificacao->format("d/m/Y");

                $object->dataatendimento = TDate::date2br($object->dataatendimento);

                $horaclassificacao = new DateTime( $object->horaclassificacao );
                $object->horaclassificacao = $horaclassificacao->format("H:i");

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
            $object = new BauAtendimentoRecord( $param[ "key" ] );
            $object->delete();
            TTransaction::close();

            $this->onReload( $param );

            new TMessage( "info", "O Registro foi apagado com sucesso!" );

        } catch ( Exception $ex ) {
            TTransaction::rollback();
            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReload( $param )
    {
        try {

            TTransaction::open( "database" );

            $repository = new TRepository( "BauAtendimentoRecord" );

            $properties = [
                "order" => "dataatendimento",
                "direction" => "asc"
            ];

            $limit = 10;

            $criteria = new TCriteria();
            $criteria->setProperties( $properties );
            $criteria->setProperty( "limit", $limit );
            $criteria->add( new TFilter( "bau_id", "=", $param[ "fk" ] ) );

            $objects = $repository->load( $criteria, FALSE );

            if ( isset( $objects ) ) {

                $this->datagrid->clear();

                foreach ( $objects as $object ) {

                    //$dataclassificacao = new DateTime( $object->dataclassificacao );

                    //$object->dataclassificacao = $dataclassificacao->format("d/m/Y");

                    $object->dataatendimento = TDate::date2br($object->dataatendimento);
                    $horaclassificacao = new DateTime( $object->horaclassificacao );
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

    public function onClear()
    {
        $this->form->clear();
    }

    public function onSaveFrames( $param = null )
    {
        try {

            $object = $this->unSetFields( $param );

            TTransaction::open( "database" );

            if ( isset( $object ) ) {
                $object->store();
            } else {
                $this->onError();
            }

            TTransaction::close();

            $this->onReloadFrames( $param );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onDeleteFrames( $param = null )
    {
        try {

            TTransaction::open( "database" );

            $object = $this->getFrameItem( $param );

            if ( isset( $object ) ) {
                $object->delete();
            } else {
                $this->onError();
            }

            TTransaction::close();

            $this->onReloadFrames( $param );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReloadFrames( $param = null )
    {
        try {

            TTransaction::open('database');

            $object = new PacienteRecord( $param[ "did" ] );

            if ( isset( $object ) ) {

                foreach ( $object->getComorbidades() as $comorbidade ) {
                    $this->framegrid1->addItem( $comorbidade );
                }

                /*
                foreach ( $object->getMedicacoes() as $medicacao ) {
                    $this->framegrid2->addItem( $medicacao );
                }

                foreach ( $object->getAlergias() as $alergia ) {
                    $this->framegrid3->addItem( $alergia );
                }
                */

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
                //unset( $object->medicamento_id );
                //unset( $object->principioativo_id );

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

        if ( isset( $object ) ) {

            unset( $object->id );
            unset( $object->profissional_id );
            unset( $object->paciente_nome );
            unset( $object->dataatendimento );
            unset( $object->horaatendimento );
            unset( $object->exameclinico );
            unset( $object->examescomplementares );
            unset( $object->descricaotratamento );

            return $object;

        } else {

            return null;

        }

    }

    public function getFrameItem( $param = null )
    {
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

        return isset( $object ) ? $object : null;
    }

    public function onError()
    {
        $action = new TAction( [ "PacientesClassificacaoRiscoList", "onReload" ] );

        new TMessage( "error", "Uma instabilidade momentâneo no sistema impediu a ação, tente novamente mais tarde.", $action );
    }
}
