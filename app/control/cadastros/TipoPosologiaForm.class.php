<?php

class TipoPosologiaForm extends TWindow
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
        $nome               = new TEntry('nometipoposologia');
        $qtdpordia          = new TEntry('qtdpordia');
        $antesrefeicao      = new TRadioGroup('antesrefeicao');
        $aposrefeicao       = new TRadioGroup('aposrefeicao');
        $apenasrefeicao     = new TRadioGroup('apenasrefeicao');

        $antesrefeicao->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $antesrefeicao->setLayout('horizontal');
        $aposrefeicao->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $aposrefeicao->setLayout('horizontal');
        $apenasrefeicao->addItems(array('SIM'=>'SIM', 'NAO'=>'NÃO'));
        $apenasrefeicao->setLayout('horizontal');

        $antesrefeicao->setValue('NAO');
        $aposrefeicao->setValue('NAO');
        $apenasrefeicao->setValue('NAO'); 

        $nome->addValidation( TextFormat::set( "Nome" ), new TRequiredValidator );
        $qtdpordia->addValidation( TextFormat::set( "Quantidade" ), new TRequiredValidator );

        $this->form->addFields([new TLabel("Nome :$redstar")], [$nome]);
        $this->form->addFields([new TLabel("Quantidade :$redstar")], [$qtdpordia]);
        $this->form->addFields([new TLabel("Antes da Refeição :$redstar")], [$antesrefeicao]);
        $this->form->addFields([new TLabel("Após Refeição] :$redstar")], [$aposrefeicao]);
        $this->form->addFields([new TLabel("Somente nas Refeições :$redstar")], [$apenasrefeicao]);
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
                $object = $this->form->getData("TipoPosologiaRecord");
                $object->store();
            TTransaction::close();

            $action = new TAction( [ "TipoPosologiaList", "onReload" ] );

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
                    $object = new TipoPosologiaRecord($param["key"]);
                    $this->form->setData($object);
                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
