# Módulo Magento 2

Esse módulo é suportado pelas versões 2.2 e 2.3 e os requisitos são os mesmo das respectivas versões da plataforma Magento.

## Instalação

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
