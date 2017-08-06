<?php

class EspecialidadeForm extends TPage
{
    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "form_especialidade" );
        $this->form->setFormTitle( "Formulário de Especialidades" );
        $this->form->class = "tform";

        $id                 = new THidden( "id" );
        $clinica_id         = new TDBCombo( "clinica_id", "database", "ClinicaRecord", "id", "nomeclinica", "nomeclinica" );
        $nomeespecialidade  = new TEntry( "nomeespecialidade" );

        $clinica_id->setDefaultOption( "..::SELECIONE::.." );
        $nomeespecialidade->setProperty( "title", "O campo é obrigatório" );

        $clinica_id->setSize( "38%" );
        $nomeespecialidade->setSize( "38%" );

        $label01 = new RequiredTextFormat( [ "Clinica", "#F00", "bold" ] );
        $label02 = new RequiredTextFormat( [ "Especialidade", "#F00", "bold" ] );

        $clinica_id->addValidation( $label01->getText(), new TRequiredValidator );
        $nomeespecialidade->addValidation( $label02->getText(), new TRequiredValidator );

        $this->form->addFields( [ new TLabel( "Especialidade", "#F00" ) ], [ $nomeespecialidade ] );
        $this->form->addFields( [ new TLabel( "Clinica", "#F00" ) ],[ $clinica_id ] );
        $this->form->addFields( [ $id] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "EspecialidadeList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 90%";
        // $container->add( new TXMLBreadCrumb( "menu.xml", "EspecialidadeList" ) );
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

            $object = $this->form->getData( "EspecialidadeRecord" );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "EspecialidadeList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action);

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar salvar o registro!<br><br><br><br>" . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try {

            if( isset( $param[ "key" ] ) ) {

                TTransaction::open( "database" );

                $object = new EspecialidadeRecord( $param[ "key" ] );

                $this->form->setData( $object );

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );
        }
    }
}
