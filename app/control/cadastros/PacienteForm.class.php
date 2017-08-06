<?php

class PacienteForm extends TPage
{

    private $form;

    public function __construct()
    {
        parent::__construct();

        $this->form = new BootstrapFormBuilder( "form_cadastro_paciente" );
        $this->form->setFormTitle( "Cadastro de Pacientes" );
        $this->form->class = "tform";

        $id               = new THidden( "id" );
        $grauinstrucao_id = new TCombo ( "grauinstrucao_id" );
        $profissao_id     = new TCombo ( "profissao_id" );
        $nomepaciente     = new TEntry ( "nomepaciente" );
        $cpf              = new TEntry ( "cpf" );
        $datanascimento   = new TDate  ( "datanascimento" );
        $sexo             = new Tcombo ( "sexo" );
        $gruposanguineo   = new TCombo ( "gruposanguineo" );
        $fatorrh          = new TCombo ( "fatorrh" );
        $numeroidentidade = new TEntry ( "numeroidentidade" );
        $orgaoidentidade  = new TEntry ( "orgaoidentidade" );
        $ufidentidade     = new TCombo ( "ufidentidade" );
        $estadocivil      = new Tcombo ( "estadocivil" );
        $endereco         = new TEntry ( "endereco" );
        $bairro           = new TEntry ( "bairro" );
        $cidade           = new TEntry ( "cidade" );
        $uf               = new TCombo ( "uf" );
        $numerosus        = new TEntry ( "numerosus" );
        $telresidencial   = new TEntry ( "telresidencial" );
        $telcelular       = new TEntry ( "telcelular" );
        $telcomercial     = new TEntry ( "telcomercial" );
        $nomemae          = new TEntry ( "nomemae" );
        $nomepai          = new TEntry ( "nomepai" );

        $items = array();
        TTransaction::open('database');
            $repository = new TRepository('ProfissaoRecord');
            $criteria = new TCriteria;
            $criteria->setProperty('order', 'nomeprofissao');

            $cadastros = $repository->load($criteria);

            foreach ($cadastros as $object)
            {
                $items[$object->id] = $object->nomeprofissao;
            }
            $profissao_id->addItems($items);
        TTransaction::close();

        $items = array();
        TTransaction::open('database');
            $repository = new TRepository('GrauinstrucaoRecord');
            $criteria = new TCriteria;
            $criteria->setProperty('order', 'nomegrauinstrucao');

            $cadastros = $repository->load($criteria);

            foreach ($cadastros as $object)
            {
                $items[$object->id] = $object->nomegrauinstrucao;
            }
            $grauinstrucao_id->addItems($items);
        TTransaction::close();

        $items = array();
        TTransaction::open('database');
            $repository = new TRepository('EstadosRecord');
            $criteria = new TCriteria;
            $criteria->setProperty('order', 'sigla');

            $cadastros = $repository->load($criteria);

            foreach ($cadastros as $object)
            {
                $items[$object->sigla] = $object->sigla;
            }
            $uf->addItems($items);
            $ufidentidade->addItems($items);
        TTransaction::close();

        $nomepaciente->setSize("35%");
        $endereco->setSize("40%");
        $bairro->setSize("30%");
        $nomemae->setSize("30%");
        $nomepai->setSize("30%");

        $nomepaciente->setProperty ( 'title', 'Digitar nome do paciente' );
        $endereco->setProperty     ( 'title', 'Digitar endereço do paciente');
        $bairro->setProperty       ( 'title', 'Digitar bairro do paciente' );
        $cidade->setProperty       ( 'title', 'Digitar cidade do paciente' );
        $uf->setDefaultOption      ( '..::SELECIONE::..' );
        $fatorrh->setDefaultOption ( '..::SELECIONE::..' );
        $cpf->setProperty          ( 'placeholder', 'Exemplo 999-999-999-99' );
        $cpf->setMask('999-999-999-99');
        $datanascimento->setMask ( 'dd/mm/yyyy' );
        $datanascimento->setDatabaseMask('yyyy-mm-dd');

        $sexo->addItems( [ "M" => "Masculino", "F" => "Feminino" ] );
        $fatorrh->addItems( [ "P" => "Positivo", "N" => "Negativo" ] );
        $estadocivil->addItems( [ "Solteiro" => "Solteiro", "Casado" => "Casado", 'Divorciado' => 'Divorciado', 'Viuvo' => 'Viuvo' ] );
        $gruposanguineo->addItems( [ "A" => "A", "B" => "B", "AB" => "AB", "O" => "O" ] );
        //$uf->addItems( [ 'AC' => 'Acre (AC)', 'AL' => 'Alagoas (AL)', 'AM' => 'Amazonas (AM)', 'AP' => 'Amapá (AP)', 'BA' => 'Bahia (BA)', 'CE' => 'Ceará (CE)', 'DF' => 'Distrito Federal (DF)', 'ES' => 'Espírito Santo (ES)', 'GO' => 'Goiás (GO)', 'MA' => 'Maranhão (MA)', 'MT' => 'Mato Grosso (MT)', 'MS' => 'Mato Grosso do Sul (MS)', 'MG' => 'Minas Gerais (MG)', 'PA' => 'Pará (PA)', 'PB' => 'Paraíba (PB)', 'PR' => 'Paraná (PR)', 'PE' => 'Pernambuco (PE)', 'PI' => 'Piauí (PI)', 'RJ' => 'Rio de Janeiro (RJ)', 'RN' => ' Rio Grande do Norte (RN)', 'RS' => ' Rio Grande do Sul (RS)', 'RO' => 'Rondônia (RO)', 'PR' => 'Roraima (RR)', 'SC' => 'Santa Catarina (SC)', 'SP' => 'São Paulo (SP)', 'SE' => 'Sergipe (SE)', 'TO' => 'Tocantins (TO)'] );

        $this->form->addFields( [ new TLabel( 'Nome do Paciente:<font color=red><b>*</b></font>' ) ], [ $nomepaciente ] );
        $this->form->addFields( [ new TLabel( 'CPF:<font color=red>*</font>' ) ], [ $cpf ] );
        $this->form->addFields( [ new TLabel( 'Endereço:<font color=red><b>*</b></font>' ) ], [ $endereco ]);
        $this->form->addFields( [ new TLabel( 'Bairro:<font color=red><b>*</b></font> ' ) ], [ $bairro ] );
        $this->form->addFields( [ new TLabel( 'Cidade:<font color=red><b>*</b></font> ' ) ], [ $cidade ] );
        $this->form->addFields( [ new TLabel( 'UF:<font color=red><b>*</b></font>' ) ], [ $uf ] );
        $this->form->addFields( [ new TLabel( 'Data de Nascimento:<font color=red><b>*</b></font>' ) ], [ $datanascimento ] );
        $this->form->addFields( [ new TLabel( 'Sexo:<font color=red><b>*</b></font>' ) ], [ $sexo ] );
        $this->form->addFields( [ new TLabel( 'Número SUS:<font color=red><b>*</b></font>' ) ], [ $numerosus ] );
        $this->form->addFields( [ new TLabel( 'Telefone Residêncial:<font color=red><b>*</b></font>' ) ], [ $telresidencial ] );
        $this->form->addFields( [ new TLabel( 'Telefone Celular:<font color=red><b>*</b></font>' ) ], [ $telcelular ] );
        $this->form->addFields( [ new TLabel( 'Telefone Comercial:<font color=red><b>*</b></font>' ) ], [ $telcomercial ] );
        $this->form->addFields( [ new TLabel( 'Nome da Mãe:<font color=red><b>*</b></font>' ) ], [ $nomemae ] );
        $this->form->addFields( [ new TLabel( 'Nome do Pai:<font color=red><b>*</b></font>' ) ], [ $nomepai ] );
        $this->form->addFields( [ new TLabel( 'Grupo Sanguineo:<font color=red><b>*</b></font>' ) ], [ $gruposanguineo ] );
        $this->form->addFields( [ new TLabel( 'Fator RH:<font color=red><b>*</b></font>' ) ], [ $fatorrh ] );
        $this->form->addFields( [ new TLabel( 'Número da Identidade:<font color=red><b>*</b></font>' ) ], [ $numeroidentidade ] );
        $this->form->addFields( [ new TLabel( 'Orgão da Identidade:<font color=red><b>*</b></font>' ) ], [ $orgaoidentidade ] );
        $this->form->addFields( [ new TLabel( 'UF Identidade:<font color=red><b>*</b></font>' ) ], [ $ufidentidade ] );
        $this->form->addFields( [ new TLabel( 'Estado Civil:<font color=red><b>*</b></font>' ) ], [ $estadocivil ] );
        $this->form->addFields( [ new TLabel( 'Grau Instrucao:<font color=red><b>*</b></font>' ) ], [ $grauinstrucao_id ] );
        $this->form->addFields( [ new TLabel( 'Profissao:<font color=red><b>*</b></font>'      ) ], [ $profissao_id ] );


        $this->form->addFields( [new TLabel('<font color=red><b>* Campos Obrigatórios </b></font>'), []] );
        $this->form->addFields( [ $id ] );

        $this->form->addAction( 'Voltar para a listagem', new TAction( [ 'PacienteList', 'onReload' ] ), 'fa:table blue' );
        $this->form->addAction( 'Salvar', new TAction( [ $this, 'onSave' ] ), 'fa:floppy-o' );

        $container = new TVBox();
        $container->style = "width: 90%";
        // $container->add( new TXMLBreadCrumb( "menu.xml", "PacienteList" ) );
        $container->add( $this->form );
        parent::add( $container );
}

    public function onSave()
    {
        try
        {
            $this->form->validate();

            TTransaction::open( 'database' );

            $object = $this->form->getData( 'PacienteRecord' );
            $object->store();

            TTransaction::close();

            $action = new TAction( [ 'PacienteList', 'onReload' ] );

            new TMessage( 'info', 'Registro salvo com sucesso!', $action );

        } catch ( Exception $ex )
        {
            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar salvar o registro!<br><br>' . $ex->getMessage() );
        }
    }

    public function onEdit( $param )
    {
        try
        {
            if( isset( $param[ 'key' ] ) )
            {
                TTransaction::open( 'database' );

                $object = new PacienteRecord( $param[ 'key' ] );
                $object->nascimento = TDate::date2br( $object->nascimento );

				if ( ! isset($object->cidade) ){
					$object->cidade = "NATAL";
				}

                $this->form->setData( $object );

                TTransaction::close();
            }
        }catch ( Exception $ex )
        {
            TTransaction::rollback();

            new TMessage( 'error', 'Ocorreu um erro ao tentar carregar o registro para edição!<br><br>' . $ex->getMessage() );
        }
    }

}
