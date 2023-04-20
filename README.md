<h1 align="center">AutoClicker</h1>

<h1 align="center">Um plugin de AutoClicker Mob´s para PocketMine 3.</h1>
    </a>
<div align="center">
	<a href="https://www.blazehosting.com.br/discord">
        <img src="https://img.shields.io/badge/Discord-7289DA?style=for-the-badge&logo=discord&logoColor=white" alt="discord">
    </a>

## Uso

Antes de mais nada, adicione o plugin à pasta de plugins e, em seguida, siga as etapas necessárias para configurá-lo adequadamente.

| Configuração | Descrição |
| --- | --- |
| Tempo | Especifique o tempo para hitar em Tick do Minecraft, não é recomendável abaixar esse valor. [`Padrão: 1`](https://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/resources/config.yml#Lhttps://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/resources/config.yml#:~:text=Tempo%3A%20%221%22%20%23%20A%20cada%201%20segundo%20um%20hit%2C%20n%C3%A3o%20%C3%A9%20recomend%C3%A1vel%20abaixar%20esse%20valor.) |
| Mundos_permitidos | [`Padrão: mundo1, mundo2, mundo3`](https://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/resources/config.yml#Lhttps://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/resources/config.yml#:~:text=Mundos_permitidos%3A,%2D%20mundo3) |

## Como funciona?

Quando um jogador executa o comando [`/autoclicker`](https://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/src/Raphael/Hit/Blaze.php#Lhttps://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/src/Raphael/Hit/Blaze.php#:~:text=if%20(%24cmd%2D%3EgetName()%20%3D%3D%3D%20%22autoclicker%22)%20%7B), o plugin verifica a existência de uma entidade em um raio de 1 bloco. Se houver uma entidade e ela for um mob vivo, o plugin verifica se o jogador está olhando para ela. Se sim, o jogador começa a atacar a entidade automaticamente. Caso o jogador mude a mira ou se afaste do raio de busca, o plugin reinicia a procura por uma nova entidade.

Com o objetivo de prevenir bugs e evitar conflitos de desempenho, o autoclicker é desativado automaticamente caso o jogador mude para um mundo que não esteja permitido na configuração.

## Moderação

Com o intuito de moderação e para evitar dúvidas se um jogador está usando um autoclicker "cheat", é possível executar o comando [`/autoclicker ver`](https://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/src/Raphael/Hit/Blaze.php#Lhttps://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/src/Raphael/Hit/Blaze.php#Lhttps://github.com/Raphael1S/AutoClicker/blob/PMMP-3/AutoClicker/src/Raphael/Hit/Blaze.php#:~:text=if%20(isset(%24args%5B0%5D)%20%26%26%20%24args%5B0%5D%20%3D%3D%3D%20%22ver%22)%20%7B). Esse comando irá verificar se o jogador está usando o autoclicker e retornará a informação. Qualquer jogador pode utilizar esse comando.
