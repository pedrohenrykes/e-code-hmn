<?php

class ProfissionalForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Profissionais" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_profissional" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                  = new THidden( "id" );
        $nomeprofissional    = new TEntry( "nomeprofissional" );
        $numeroconselho      = new TEntry( "numeroconselho" );
        $tipoprofissional_id = new TDBCombo( "tipoprofissional_id ", "database", "TipoProfissionalRecord", "id", "nometipoprofissional", "nometipoprofissional" );

        //$nomeprofissional->forceUpperCase();
        //$numeroconselho->setMask( "A!" );

        $nomeprofissional->setProperty("title", "O campo e obrigatorio");
        $numeroconselho ->setProperty("title", "O campo e obrigatorio");
        $tipoprofissional_id ->setProperty("title", "O campo e obrigatorio");

        $nomeprofissional->setSize("38%");
        $numeroconselho->setSize("38%");
        $tipoprofissional_id->setSize("38%");

        $nomeprofissional->addValidation( TextFormat::set( "Nome do Profissional" ), new TRequiredValidator );
        $numeroconselho->addValidation( TextFormat::set( "Numero do Conselho" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome do Profissional: $redstar")], [$nomeprofissional]);
        $this->form->addFields([new TLabel("Numero do Conselho: $redstar")], [$numeroconselho]);
        $this->form->addFields([new TLabel("Tipo de profissional: $redstar")], [$tipoprofissional_id]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "ProfissionalList", "onReload" ] ), "fa:table blue" );

        $container = new TVBox();
        $container->style = "width: 100%";
        $container->add( $this->form );

        parent::add( $container );
    }

    public function onSave()
    {
        try {

            $this->form->validate();

            TTransaction::open( "database" );

                $object = $this->form->getData("ProfissionalRecord");
                $object->store();

            TTransaction::close();

            $action = new TAction( [ "ProfissionalList", "onReload" ] );

            new TMessage( "info", "Registro salvo com sucesso!", $action );

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

                $object = new ProfissionalRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
