<?php

namespace Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    private static $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes (
                        id INTEGER primary key,
                        descricao  TEXT,
                        finalizado BOOL,
                        dataInicio TEXT
                     );');
    }

    protected function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        // arrange
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes as $leilao){
            $leilaoDao->salva($leilao);
        }

        //act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        //assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame(
            'Variante 0Km',
            $leiloes[0]->recuperarDescricao()
        );
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        // arrange
        $leilaoDao = new LeilaoDao(self::$pdo);
        foreach ($leiloes as $leilao){
            $leilaoDao->salva($leilao);
        }

        //act
        $leiloes = $leilaoDao->recuperarFinalizados();

        //assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame(
            'Fiat 147 0Km',
            $leiloes[0]->recuperarDescricao()
        );
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }

    public function leiloes()
    {
        $naoFinalizado = new Leilao('Variante 0Km');
        $finalizado = new Leilao('Fiat 147 0Km');
        $finalizado->finaliza();
        return [
            [
                [$naoFinalizado, $finalizado]
            ]
        ];
    }
}

/*
 * 1.6 Inserindo e buscando
 * Agora o PHPUnit detecta o erro na query SQL
 *
 * 2.2 Removendo após o teste
 * Usamos a função tearDown() para deletar todos os dados do banco e poder normalizar os testes.
 * Contudo, apagar todos os dados do banco não é recomendado.
 *
 * 2.4 Trabalhando com transações
 * Usando as funções beginTransaction() e roolback() dentro dos métodos setUp e tearDown, resolve o problema de alterar
 * a base de dados sem necessidade.
 * Porém, em escala isso continua sendo um problema, bancos muitos populados ou com baixa eficiência podem causar
 * lentidão nos testes, então é preciso uma solução que use um banco de dados, mas que não seja o mesmo de produção.
 *
 * 2.6 Fakes SQLite em memória
 * O SQLite passa ser executado em memória e através do setUpBeforeClass, para executar a query de criação da estrutura
 * antes de qualquer outro teste. Como o método setUp executa todas as vezes antes de um teste, então para otimizar essa
 * estrutura foi movida enquando só as transactions são executadas.
 *
 * 3.2 Buscando finalizados
 * Reorganizando o código com mais um teste e preparando um dataProvider para evitar duplicação de código
 */