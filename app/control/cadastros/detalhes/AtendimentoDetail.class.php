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
        $profissional_id        = new THidden( "profissional_id" );
        $paciente_nome          = new TEntry( "paciente_nome" );
        $dataclassificacao      = new TDate( "dataatendimento" );
        $exameclinico           = new TText( "exameclinico" );
        $examescomplementares   = new TText( "examescomplementares" );
        $descricaotratamento    = new TText( "descricaotratamento" );

        $paciente_nome->setSize("45%");
        $dataclassificacao->setSize("45%");
        $exameclinico->setSize("90%");
        $examescomplementares->setSize("90%");
        $descricaotratamento->setSize("90%");

        $fk = filter_input( INPUT_GET, "fk" );
        $did = filter_input( INPUT_GET, "did" );

        $bau_id->setValue ($fk);
        $paciente_id->setValue ($did);

        try {
            TTransaction::open( "database" );
            $paciente = new PacienteRecord($did);
            if( isset($paciente) ) {
                $paciente_nome->setValue($paciente->nomepaciente);
            }
            TTransaction::close();
        } catch (Exception $ex) {
            $action = new TAction(["PacientesAtendimentoList", "onReload"]);
            new TMessage("error", "Ocorreu um erro ao carregar as dependência do formulário.", $action);
        }


        $dataclassificacao->setMask( "dd/mm/yyyy h:i:s" );
        $dataclassificacao->setDatabaseMask("yyyy-mm-dd h:i:s");

        $dataclassificacao->setValue( date( "d/m/Y h:i:s" ) );
        $dataclassificacao->setEditable( false );

        $paciente_nome->setEditable( false );
        $paciente_nome->forceUpperCase();

        $dataclassificacao->addValidation( TextFormat::set( "Data da Avaliação" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Paciente: {$redstar}" ) ], [ $paciente_nome ] );
        $this->form->addFields( [ new TLabel( "Data: {$redstar}" ) ], [ $dataclassificacao ] );
        $this->form->addFields( [ new TLabel( "Avaliação Médica:" ) ], [ $exameclinico ] );


        /*--- frame de Diagnostico ---*/
        $frame1 = new TFrame;
        $frame1->setLegend( "Comorbidades do Paciente" );
        $frame1->style .= 'margin-left:17%;width:73%;';


        $this->form->addContent( [ $frame1 ] );

        $this->framegrid1 = new TQuickGrid();
        $this->framegrid1->setHeight('0%');
        $this->framegrid1->makeScrollable();
        $this->framegrid1->style='width: 100%';
        $this->framegrid1->id = 'framegrid1';
        $this->framegrid1->disableDefaultClick();

        $this->framegrid1->addQuickColumn( "Patologia", 'cid_codnome', 'left', '1%');
        $this->framegrid1->createModel();

        $hbox1 = new THBox;
        $hbox1->style = 'margin: 0%';
        $vbox1 = new TVBox;
        $vbox1->style = 'width:100%';
        $vbox1->add( $hbox1 );
        $vbox1->add( $this->framegrid1 );
        $frame1->add( $vbox1 );
        /*--------------------------------------*/

        $this->form->addFields( [ $id, $bau_id, $paciente_id, $profissional_id ] );

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

        $column_2 = new TDataGridColumn( "responsavel_nome", "Responsável", "left" );
        $column_3 = new TDataGridColumn( "dataatendimento", "Data", "left" );

        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );

        $action1 = new CustomDataGridAction(array('PrescreverMedicacaoDetail', 'onReload'));
        $action1->setLabel('Prescrever Medicação');
        $action1->setImage('fa:file-text blue');
        $action1->setField( "id" );
        $action1->setFk( "id" );
        $action1->setDid( "bau_id" );

        $action4 = new CustomDataGridAction(array('SolicitarExameDetail', 'onReload'));
        $action4->setLabel('Solicitar Exame');
        $action4->setImage('fa:file-text blue');
        $action4->setField( "id" );
        $action4->setFk( "id" );
        $action4->setDid( "bau_id" );

        $action5 = new CustomDataGridAction(array('EvolucaoPacienteDetail', 'onReload'));
        $action5->setLabel('Evolução');
        $action5->setImage('fa:file-text blue');
        $action5->setField( "id" );
        $action5->setFk( "id" );
        $action5->setDid( "bau_id" );

        $action2 = new CustomDataGridAction(array($this, 'onDelete'));
        $action2->setLabel('Deletar');
        $action2->setImage('bs:remove red');
        $action2->setField('id');
        $action2->setParameter( "fk", $fk );
        $action2->setParameter( "did", $did );

        $action3 = new CustomDataGridAction(array($this, 'onEdit'));
        $action3->setLabel('Editar');
        $action3->setImage('fa:pencil-square-o blue fa-lg');
        $action3->setField('id');
        $action3->setParameter( "fk", $fk );
        $action3->setParameter( "did", $did );

        $action_group = new TDataGridActionGroup('Ações', 'bs:th');

        $action_group->addHeader('Solicitações');
        $action_group->addAction($action1);
        $action_group->addAction($action4);
        $action_group->addAction($action5);
        $action_group->addSeparator();
        $action_group->addHeader('Correções');
        $action_group->addAction($action2);
        $action_group->addAction($action3);

        $this->datagrid->addActionGroup($action_group);
        /*

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

        $action_presc = new CustomDataGridAction( [ "PrescreverMedicacaoDetail", "onReload" ] );
        $action_presc->setButtonClass( "btn btn-primary" );
        $action_presc->setImage( "fa:user-md white fa-lg" );
        $action_presc->setField( "id" );
        $action_presc->setFk( "id" );
        $action_presc->setDid( "bau_id" );
        $action_presc->setUseButton(TRUE);
        $this->datagrid->addQuickAction( "Prescrever Medicação", $action_presc, 'id');
        */

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

            $param = [
            "fk"  => $param[ "fk" ],
            "did"  => $param[ "did" ]
            ];

        try {
            $object->profissional_id = TSession::getValue('profissionalid');
            
/*
            var_dump($_SESSION);
            exit();
*/
            unset( $object->paciente_nome );
            unset( $object->cid_id );


            $this->form->validate();
            TTransaction::open( "database" );

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
                $object->dataatendimento = TDate::date2br($object->dataatendimento) . ' ' . substr($object->dataatendimento, 11, strlen($object->dataatendimento));

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
            "direction" => "desc"
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

                    $object->dataatendimento = TDate::date2br($object->dataatendimento) . ' ' . substr($object->dataatendimento, 11, strlen($object->dataatendimento));

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
            $object->cid_id = key($object->cid_id);

            TTransaction::open( "database" );

            if ( isset( $object ) ) {
                $object->store();
            } else {
                $this->onError();
            }

            TTransaction::close();

            $this->onReload($param);

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onDeleteFrames( $param = null ){
        try {

            TTransaction::open( "database" );


            $object = $this->getFrameItem( $param );

            if ( isset( $object ) ) {
                $object->delete();
            } else {
                $this->onError();
            }

            TTransaction::close();

            $this->onReload( $param );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", $ex->getMessage() );

        }
    }

    public function onReloadFrames( $param = null )
    {
        try {

            TTransaction::open('database');

            $object = new BauRecord( $param[ "fk" ] );
            $paciente2 = new PacienteRecord( $object->paciente_id );

            if ( isset( $paciente2 ) ) {

                foreach ( $paciente2->getComorbidades() as $comorbidade ) {
                    $this->framegrid1->addItem( $comorbidade );
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
        $action = new TAction( [ "AtendimentoDetail", "onReload" ] );

        new TMessage( "error", "Uma instabilidade momentâneo no sistema impediu a ação, tente novamente mais tarde.", $action );
    }
}
