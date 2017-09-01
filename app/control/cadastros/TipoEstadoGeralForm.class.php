<?php

class TipoEstadoGeralForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Estado Geral" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_tipoestadogeral" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id = new THidden( "id" );
        $nomeestadogeral = new TEntry( "nomeestadogeral" );

        //$nomeprofissional->forceUpperCase();
        //$numeroconselho->setMask( "A!" );

        $nomeestadogeral->setProperty("title", "O campo e obrigatorio");
        $nomeestadogeral->setSize("38%");
        $nomeestadogeral->addValidation( TextFormat::set( "Nome do Estado Geral" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome do Estado Geral: $redstar")], [$nomeestadogeral]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );

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

                $object = $this->form->getData("TipoEstadoGeralRecord");
                $object->store();

            TTransaction::close();

            $action = new TAction( [ "TipoEstadoGeralList", "onReload" ] );

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

                $object = new TipoEstadoGeralRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
