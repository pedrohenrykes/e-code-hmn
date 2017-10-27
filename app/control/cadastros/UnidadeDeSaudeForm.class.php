<?php

class UnidadeDeSaudeForm extends TWindow
{
    private $form;

    public function __construct()
    {
        parent::__construct();
        parent::setTitle( "Cadastro de Profissionais" );
        parent::setSize( 0.600, 0.800 );

        $redstar = '<font color="red"><b>*</b></font>';

        $this->form = new BootstrapFormBuilder( "form_UnidadeDeSaude" );
        $this->form->setFormTitle( "($redstar) campos obrigatórios" );
        $this->form->class = "tform";

        $id        = new THidden( "id" );
        $nomeunidade = new TEntry( "nomeunidade" );
        $endereco = new TEntry( "endereco" );
        $bairro = new TEntry( "bairro" );
        $cidade = new TEntry( "cidade" );
        $cep = new TEntry( "cep" );
        $uf = new TEntry( "uf" );
        $latitude = new TEntry( "latitude" );
        $longitude = new TEntry( "longitude" );
        //$nomelocal = new TEntry( "nomelocal" );
        //$situacao = new TCombo( "situacao" );

        //$nomeunidadedesaude->forceUpperCase();
        //$numeroconselho->setMask( "A!" );

        $nomeunidade->setProperty("title", "O campo e obrigatorio");
        $nomeunidade->setSize("38%");
        $nomeunidade->addValidation( TextFormat::set( "Nome das unidades" ), new TRequiredValidator );

        $endereco->setProperty("title", "O campo e obrigatorio");
        $endereco->setSize("38%");
        $endereco->addValidation( TextFormat::set( "Endereço" ), new TRequiredValidator );

        $bairro->setProperty("title", "O campo e obrigatorio");
        $bairro->setSize("38%");
        $bairro->addValidation( TextFormat::set( "Bairro" ), new TRequiredValidator );

        $cidade->setProperty("title", "O campo e obrigatorio");
        $cidade->setSize("38%");
        $cidade->addValidation( TextFormat::set( "Cidade" ), new TRequiredValidator );

        $cep->setProperty("title", "O campo e obrigatorio");
        $cep->setSize("38%");
        $cep->addValidation( TextFormat::set( "CEP" ), new TRequiredValidator );

        $uf->setProperty("title", "O campo e obrigatorio");
        $uf->setSize("38%");
        $uf->addValidation( TextFormat::set( "UF" ), new TRequiredValidator );

        $latitude->setProperty("title", "O campo e obrigatorio");
        $latitude->setSize("38%");
        $latitude->addValidation( TextFormat::set( "Latitude" ), new TRequiredValidator );

        $longitude->setProperty("title", "O campo e obrigatorio");
        $longitude->setSize("38%");
        $longitude->addValidation( TextFormat::set( "Longitude" ), new TRequiredValidator );
        
        //combo
        //$situacao->addItems( [ "ATIVO" => "ATIVO", "INATIVO" => "INATIVO" ] );
        //$situacao->setDefaultOption( "..::SELECIONE::.." );

        $this->form->addFields([new TLabel("Nome da unidade: $redstar")], [$nomeunidade]);
        $this->form->addFields([new TLabel("Endereço: $redstar")], [$endereco]);
        $this->form->addFields([new TLabel("Bairro: $redstar")], [$bairro]);
        $this->form->addFields([new TLabel("Cidade: $redstar")], [$cidade]);
        $this->form->addFields([new TLabel("CEP: $redstar")], [$cep]);
        $this->form->addFields([new TLabel("UF: $redstar")], [$uf]);
        $this->form->addFields([new TLabel("Latitude: $redstar")], [$latitude]);
        $this->form->addFields([new TLabel("Longitude: $redstar")], [$longitude]);
        //$this->form->addFields([new TLabel("Nome das unidades de saúde: $redstar")], [$nomeunidadedesaude]);
        //$this->form->addFields([new TLabel("Situação: $redstar")], [$situacao]);
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

            $object = $this->form->getData("UnidadeDeSaudeRecord");
            $object->store();

            TTransaction::close();

            $action = new TAction( [ "UnidadeDeSaudeList", "onReload" ] );

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

                $object = new UnidadeDeSaudeRecord($param["key"]);

                $this->form->setData($object);

                TTransaction::close();
            }

        } catch ( Exception $ex ) {

            TTransaction::rollback();

            new TMessage( "error", "Ocorreu um erro ao tentar carregar o registro para edição!<br><br>" . $ex->getMessage() );

        }
    }
}
