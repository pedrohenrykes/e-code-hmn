<?php

class TipoProfissionalForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro Tipo de Profissional" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_tipo_profissional" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id                   = new THidden( "id" );
        $nometipoprofissional = new TEntry("nometipoprofissional");

        $nometipoprofissional->setProperty("title", "O campo e obrigatorio");
        $nometipoprofissional->setSize("38%");
        $nometipoprofissional->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome Tipo Profissional: $redstar")], [$nometipoprofissional]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "TipoProfissionalList", "onReload" ] ), "fa:table blue" );

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
                $object = $this->form->getData("TipoProfissionalRecord");
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "TipoProfissionalList", "onReload" ] );

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
                    $object = new TipoProfissionalRecord($param["key"]);
                    $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
