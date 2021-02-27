<?php

//classe dashboard
class Dashboard {

	public $data_inicio;
	public $data_fim;
	public $numeroVendas;
	public $totalVendas;
	public $clientes_ativos;
	public $clientes_inativos;
	public $reclamacoes_total;
	public $elogios_total;
	public $sugestao_total;
	public $despesa_total;

	public function __get($atributo) {
		return $this->$atributo;
	}

	public function __set($atributo, $valor) {
		 $this->$atributo = $valor;
		 return $this;
	}
}

//classe de coneão bd
class Conexao {
	private $host = 'localhost';
	private $dbname = 'dashboard';
	private $user = 'root';
	private $pass = '';

	public function conectar() {
		try {

			$conexao = new PDO(
				"mysql:host=$this->host;dbname=$this->dbname",
				"$this->user",
				"$this->pass"

			);

			//
			$conexao->exec('set charset utf8');

			return $conexao;

		} catch (PDOException $e) {
			echo '<p>'.$e->getMessage().'</p>';
		}
	}

}

//classe (model)
class Bd {
	private $conexao;
	private $dashboard;

	public function __construct(Conexao $conexao, Dashboard $dashboard) {
		$this->conexao = $conexao->conectar();
		$this->dashboard = $dashboard;
	}

	public function getNumeroVendas() {
		$query = '
			select
				count(*) as numero_vendas
			from
				tb_vendas
			where
				data_venda between :data_inicio and :data_fim';

		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ)->numero_vendas;
	}

	public function getTotalVendas() {
		$query = '
			select
				SUM(total) as total_vendas
			from
				tb_vendas
			where
				data_venda between :data_inicio and :data_fim';

		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();

		return $stmt->fetch(PDO::FETCH_OBJ)->total_vendas;
	}

	public function getClientesAtivos() {
		$query = '
			select
				count(*) as clientesAtivos
			from
				tb_clientes
			where
				cliente_ativo = 1';

		$stmt = $this->conexao->prepare($query);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->clientesAtivos;
	}

	public function getClientesInativos() {
		$query = '
			select
				count(*) as clientesInativos
			from
				tb_clientes
			where
				cliente_ativo = 0';

		$stmt = $this->conexao->prepare($query);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->clientesInativos;
	}

	public function getTotalDespesas() {
		$query = '
			select
				SUM(d.total) as totalDespesas
			from
				tb_despesas as d
				left join tb_vendas on (d.id = tb_vendas.id)
			where
				data_despesa between :data_inicio and :data_fim';

		$stmt = $this->conexao->prepare($query);
		$stmt->bindValue(':data_inicio', $this->dashboard->__get('data_inicio'));
		$stmt->bindValue(':data_fim', $this->dashboard->__get('data_fim'));
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->totalDespesas;
	}

	public function getTotalReclamacoes() {
		$query = '
			select
				count(*) as totalReclamacoes
			from
				tb_contatos
			where
				tipo_contato = 1';

		$stmt = $this->conexao->prepare($query);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->totalReclamacoes;
	}

	public function getTotalElogios() {
		$query = '
			select
				count(*) as totalElogios
			from
				tb_contatos
			where
				tipo_contato = 2';

		$stmt = $this->conexao->prepare($query);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->totalElogios;
	}

	public function getTotalSugestoes() {
		$query = '
			select
				count(*) as totalSugestoes
			from
				tb_contatos
			where
				tipo_contato = 3';

		$stmt = $this->conexao->prepare($query);
		$stmt->execute();
		return $stmt->fetch(PDO::FETCH_OBJ)->totalSugestoes;
	}
}

$dashboard = new Dashboard();

$conexao = new Conexao();

$competencia = explode('-', $_GET['competencia']);
$ano = $competencia[0];
$mes = $competencia[1];

$dias_do_mes = cal_days_in_month(CAL_GREGORIAN, $mes, $ano);

$dashboard->__set('data_inicio', $ano.'-'.$mes.'-01');
$dashboard->__set('data_fim',  $ano.'-'.$mes.'-'.$dias_do_mes);


$bd = new Bd($conexao, $dashboard);

$dashboard->__set('numeroVendas', $bd->getNumeroVendas());
$dashboard->__set('totalVendas', $bd->getTotalVendas());
$dashboard->__set('cli', $bd->getClientesAtivos());
$dashboard->__set('inativo', $bd->getClientesInativos());
$dashboard->__set('despesa', $bd->getTotalDespesas());
$dashboard->__set('reclamacoes', $bd->getTotalReclamacoes());
$dashboard->__set('elogio', $bd->getTotalElogios());
$dashboard->__set('sugestao', $bd->getTotalSugestoes());
echo json_encode($dashboard);




?>