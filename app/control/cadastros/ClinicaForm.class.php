<?php

class ClinicaForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Formulário de Clínicas" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_clinica" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id  = new THidden( "id" );
        $nomeclinica = new TEntry( "nomeclinica" );
        $ies_id = new TDBCombo( "ies_id", "database", "IesRecord", "id", "nomeies", "nomeies" );

        $nomeclinica->setProperty( "title", "O campo é obrigatório" );
        $ies_id->setProperty( "title", "O campo é obrigatório" );

        $ies_id->setDefaultOption( "..::SELECIONE::.." );

        $nomeclinica->setSize( "38%" );
        $ies_id->setSize( "38%" );

        $label01 = new RequiredTextFormat( [ "Nome", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Instituição", "#F00", "bold" ] );

        $nomeclinica->addValidation( $label01->getText(), new TRequiredValidator );
        $ies_id->addValidation( $label02->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Instituição: $redstar") ], [ $ies_id ] );
        $this->form->addFields( [ new TLabel( "Nome: $redstar") ], [ $nomeclinica ] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( new TXMLBreadCrumb( "menu.xml", "ClinicaList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "ClinicaRecord" );

            $object->store();

            TTransaction::close();

            $action = new TAction( [ "ClinicaList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if ( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new ClinicaRecord( $param[ "key" ] );

                TTransaction::close();

                $this->form->setData( $object );
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
