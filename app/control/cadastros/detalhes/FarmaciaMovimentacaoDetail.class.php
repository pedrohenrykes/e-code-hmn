<?php

class FarmaciaMovimentacaoDetail extends TStandardList
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

        $this->form = new BootstrapFormBuilder( "movimentacao_exame" );
        $this->form->setFormTitle( "Movimentação de Medicamentos entre farmacias" );

        parent::setDatabase('database');
        parent::setActiveRecord('FarmaciaMovimentacaoRecord');
        parent::addFilterField('nome', 'like', 'nome');

        $id     = new THidden( "id" );
        $farmaciaorigem = new TDBCombo("farmaciaorigem_id", "database", "FarmaciaRecord", "id", "nomefarmacia", "nomefarmacia");
        $farmaciadestino = new TDBCombo("farmaciadestino_id", "database", "FarmaciaRecord", "id", "nomefarmacia", "nomefarmacia");

        $id->setSize( "38%" );
        $farmaciaorigem->addValidation( TextFormat::set( "Tipo do exame" ), new TRequiredValidator );
        $farmaciadestino->addValidation( TextFormat::set( "Tipo do exame" ), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Origem:" ) ], [ $farmaciaorigem ] );
        $this->form->addFields( [ new TLabel( "Destino:" ) ], [ $farmaciadestino ] );
        $this->form->addFields( [ $id ] );

        /*-------------------------- frame de comorbidades -------------------*/
        $frame1 = new TFrame;
        $frame1->setLegend( "Solicitação" );
        $frame1->style .= ';margin:0px;width:95%';
        $cid_id = new TDBCombo ( 'cid_id', 'database', 'VwCidRecord', 'id', 'nomecid', 'nomecid' );
        $cid_id->{'placeholder'} = "Produto";
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


        $this->form->addAction( "Buscar", new TAction( [ $this, "onSearch" ] ), "fa:search" );
        $this->form->addAction( "Adicionar", new TAction( [ $this, "onSave" ] ), "bs:plus-sign green" );

        $this->datagrid = new BootstrapDatagridWrapper( new TDataGrid() );
        $this->datagrid->datatable = "true";
        $this->datagrid->style = "width: 100%";
        $this->datagrid->setHeight( 320 );

        $column_1 = new TDataGridColumn( "farmacia_origem", "Origem", "left" );
        $column_2 = new TDataGridColumn( "farmacia_destino", "Destino", "left" );
        $column_3 = new TDataGridColumn( "datasolicitacao", "Solicitação", "left" );
        $column_4 = new TDataGridColumn( "datamovimentacao", "Movimentação", "left" );
        $column_5 = new TDataGridColumn( "situacao", "Situação", "left" );

        $this->datagrid->addColumn( $column_1 );
        $this->datagrid->addColumn( $column_2 );
        $this->datagrid->addColumn( $column_3 );
        $this->datagrid->addColumn( $column_4 );
        $this->datagrid->addColumn( $column_5 );

        $action_edit = new TDataGridAction( [ $this, "onEdit" ] );
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
        $container->style = "width: 100%";
        $container->add( $this->form );
        $container->add( TPanelGroup::pack( NULL, $this->datagrid ) );
        $container->add( $this->pageNavigation );

        parent::add( $container );
    }

    public function onSave( $param = null ){
        try {

            $this->form->validate();
            $object = $this->form->getData( "FarmarciaMovimentacaoRecord" );

            TTransaction::open( "database" );
            $object->store();
            TTransaction::close();

            $action = new TAction( [ $this, "onSearch" ] );
            new TMessage( "info", "Registro Salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );

        }
    }
    function onEdit($param) {
        try {
            if (isset($param['key'])) {

                $key = $param['key'];
                TTransaction::open('database');

                $object = new FarmarciaMovimentacaoRecord($key);
                $this->form->setData($object);

                TTransaction::close();
            } else {
                $this->form->clear();
            }
        } catch (Exception $e) {

            new TMessage('error', '<b>Error</b> ' . $e->getMessage());
            TTransaction::rollback();
        }
    }

    public function onDelete($param){
        $action = new TAction(array($this, 'Delete'));
        $action->setParameters($param);
        new TQuestion(AdiantiCoreTranslator::translate('Do you really want to delete ?'), $action);
    }
    
    public function Delete($param){

        try{
            $key=$param['key'];

            TTransaction::open($this->database);
            $class = $this->activeRecord;
            $object = new $class($key);
            $object->delete();
            TTransaction::close();

            $this->onReload( $param );
            new TMessage('info', AdiantiCoreTranslator::translate('Record deleted'));
        }
        catch (Exception $e){
            new TMessage('error', '<b>O Registro possui dependências! Não é permitido exclui-lo! </b>');
            TTransaction::rollback();
        }
    }
     public function throwbackToPage()
    {
        $action = new TAction( [ "PacientesClassificacaoRiscoList", "onReload" ] );

        new TMessage( "error", "Uma instabilidade momentânea no sistema impediu a ação, tente novamente mais tarde.", $action );
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
            unset( $object->hgt );
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
    
}
