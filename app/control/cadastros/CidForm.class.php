<?php

class CidForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de C.I.D.s" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_cid" );
        $this->form->setFormTitle( "({$redstar}) campos obrigatórios" );
        $this->form->class = "tform";

        $id        = new THidden( "id" );
        $codigocid = new TEntry( "codigocid" );
        $nomecid   = new TEntry( "nomecid" );

        $codigocid->forceUpperCase();
        $codigocid->setMask( "A!" );

        $codigocid->setProperty("title", "O campo e obrigatorio");
        $nomecid->setProperty("title", "O campo e obrigatorio");

        $codigocid->setSize("38%");
        $nomecid->setSize("38%");

        // $label01 = new RequiredTextFormat( [ "Código", "#F00", "bold" ] );
        // $label02 = new RequiredTextFormat( [ "Nome", "#F00", "bold" ] );

        $codigocid->addValidation( TextFormat::set( "Código" ), new TRequiredValidator );
        $nomecid->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Código: {$redstar}")], [$codigocid]);
        $this->form->addFields([new TLabel("Nome: {$redstar}")], [$nomecid]);
        $this->form->addFields( [ $id ] );

        $this->form->addAction( "Salvar", new TAction( [ $this, "onSave" ] ), "fa:floppy-o" );
        $this->form->addAction( "Voltar para a listagem", new TAction( [ "CidList", "onReload" ] ), "fa:table blue" );

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

            $object = $this->form->getData("CidRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "CidList", "onReload" ] );

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

                $object = new CidRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
