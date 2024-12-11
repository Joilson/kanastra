###          

Nao tenho familiaridade com o Laravel, ja trabalhei com Lumen tem 7 anos. O resultado dessa entrega foi baseada no que
encontrei na documentaçao de ontem pra hj. Mas preferi utiliza-lo, pq e o framework do projeto de vcs!

Alguns pontos importantes seguindo os requisitos:

### Funcionamento

Existe uma API para iniciar o processamento do CSV

```curl
curl --location 'http://localhost:8000/api/payment-slip/process-file' \
--header 'Accept: application/json' \
--form 'file=@".../input.csv"'
``` 

e lançado um job para processamento async que:

- le o csv
- insere a modelo evitando duplicidades
- gera o boleto
- envia o email ao usuario final com o boleto em anexo
- ao termino envia um email ao cliente que forneceu a lista, o informando que terminou

Deixei ao longo do codigo alguns @TODOs que eu gostaria de ter implementado.

### Performance

O processamento do CSV foi Async. Ele e salvo na API, mas e processado apenas no worker async. Processar na request,
poderia levar a um timeout do servidor web ou do PHP.

A leitura do CSV no worker e feita de forma parcial, para que a memoria do host nao fique sobregarregada.

A ideia da fila era implementa-la em um gerenciador mais inteligente, porem priorizei outras partes,
mas eu nao utilizaria o banco como esta hj se nao fosse um teste de avaliaçao.

Se fosse necessario, daria para deixar essa versao mais ecalavel, adicionando a geraçao de boleto/envio
em outra fila, para que sejam processados mais de um evento ao mesmo tempo.

### Sobre as implementaçoes concretas

A geraçao de boleto e envio de email, recebem os dados necessarios para a execuçao, porem adicionei apenas logs como
saida.

### Os testes

Nao cobri todos os cenarios, porem adicionei alguns para que pudessem avaliar meus conhecimentos com eles.

- 1 teste unitario PaymentSlipFileReaderTest
- 1 teste funcional ProcessPaymentSlipFileTest
- 1 teste de integraçao ProcessPaymentsSlipFileTest

### Classes importantes

 - PaymentSlipFileProcessorController
 - ProcessPaymentSlipFile
 - PaymentSlipFileReader
 - PaymentSlipBuilderFactory
 - EmailDispatcher

### Rodando o projeto

```
    docker compose up --build
    chmod -R 777 storage/logs
    docker compose exec app composer install 
    docker compose exec app php artisan migrate 
    
    // Fila para consumir o job
    docker compose exec app php artisan queue:work
```
como visualizar a execuçao do codigo

```
docker compose exec app tail -f storage/logs/laravel.log 
```

Output ex.:
```
[2024-12...] local.INFO: A list of payment slips has been imported, waiting to process in the background {"persistedFilePath":"lists//81bb3d75-05b8-48d5-840a-d2293d4b9f49.csv"}
[2024-12...] local.INFO: Processing payment slip: /usr/share/nginx/storage/app/private/lists//81bb3d75-05b8-48d5-840a-d2293d4b9f49.csv  
[2024-12...] local.INFO: Payment Slip Builder is requested for new pdf file 1adb6ccf-ff16-467f-bea7-5f05d494280f   
[2024-12...] local.INFO: Payment slip file was created to send for customer {"generated":{"filePath":"generated/b503befd-12c2-48c9-81d4-b9df46cac78a.pdv","createdAt":"2024-12-11T18:27:46+00:00","uuid":"1adb6ccf-ff16-467f-bea7-5f05d494280f "}}
[2024-12...] local.INFO: Email communication dispatched {"data":{"body":"Hi John Doe new payment was available for payment, please see attachments.","sender":"payments@kanastra.com","receiver":"johndoe@kanastra.com.br","subject":"Hy John Doe, you will be poorer","attachments":["generated/b503befd-12c2-48c9-81d4-b9df46cac78a.pdv"],"paymentSlipDebtId":"1adb6ccf-ff16-467f-bea7-5f05d494280f "}}
[2024-12...] local.INFO: Email communication dispatched {"data":{"body":"Hy your list is already processed with 1 success items","sender":"larissa@kanastra.com","receiver":"john@itau.com","subject":"getting rich","attachments":[],"customerEmail":"john@itau.com"}}
```


### Analise estatica de codigo

instalei o phpstan e o phpcs no projeto

```
docker compose exec app vendor/bin/phpstan analyse app
```

rodar os testes

```
docker compose exec app php artisan test
```



