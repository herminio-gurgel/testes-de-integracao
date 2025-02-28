<?php

namespace Integration\Web;

use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    public function testApiRestDeveRetornarArrayDeLeiloes()
    {
        $resposta = file_get_contents('http://localhost:8080/rest.php');
        self::assertStringContainsString('200 OK', $http_response_header[0]);
        self::assertIsArray(json_decode($resposta));
    }
}

/*
 * 5.3 Testes de endpoint
 * Para testar API é preciso fazer requisições HTTP e com base nas respostas fazer os asserts apropriados.
 * Observação importante: no exemplo foi preciso subir um servidor web para executar o teste.
 * Entretanto é mais interessante que os testes não dependam de infraestrutura, então estude sobre os frameworks e quais
 * ferramentas eles possuem para executarem as requisições sem dependerem de um servidor rodando o projeto.
 */