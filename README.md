# Importante

Também é possível fazer o download da [última release](https://github.com/DevelopersRede/magento2/releases/latest/download/magento.zip). Essa versão já contém as dependências, então basta descompactar o pacote e enviá-lo para o servidor da plataforma.

# Funcionalidades

Este plugin possui as seguintes funcionalidades:
* autorização com captura direta ou posterior
* captura
* cancelamento

A função 3DS1 foi descontinuada e será atualizada nas próximas versões do plugin.

# Módulo Magento 2

Esse módulo é suportado pelas versões 2.2, 2.3 e 2.4 e os requisitos são os mesmo das respectivas versões da plataforma Magento.

## Instalação via composer

Esse módulo utiliza o SDK PHP como dependência e foi desenvolvido para ser instalado via composer. Para instalá-lo, no diretório da instalação da sua plataforma Magento, execute:

```bash
composer require developersrede/magento2
```

Isso fará o download do módulo, do SDK e suas dependências. Após adicionado o módulo, uma atualização da instalação do Magento é necessária:

```bash
php bin/magento setup:upgrade
```

E uma limpeza de cache:

```bash
php bin/magento cache:flush
```

Pronto. O módulo da Rede para o Magento 2 está pronto para ser configurado.

## Instalação via release

Caso prefira, é possível instalar o módulo através de sua release. Para isso, basta **ir à raiz da instalação da plataforma Magento** e seguir os seguintes passos:

1. Crie o diretório do módulo:
   * `mkdir -p app/code/Rede/Adquirencia`
2. Faça o download da última release do módulo:
   * `wget wget https://github.com/DevelopersRede/magento2/releases/latest/download/magento.zip -P app/code/Rede/Adquirencia/`
3. Descompacte o módulo:
   * `unzip app/code/Rede/Adquirencia/magento.zip -d app/code/Rede/Adquirencia/`
4.Apague o zip:
   * `rm app/code/Rede/Adquirencia/magento.zip`
5. Instale o SDK PHP:
   * `composer require developersrede/erede-php`
6. Atualize a instalação:
   * `php bin/magento setup:upgrade`
