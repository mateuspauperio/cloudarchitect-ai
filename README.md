# CloudArchitect AI

Gerador intuitivo de arquiteturas para nuvem AWS. Você descreve os requisitos do projeto (ou escolhe um modelo pronto) e a aplicação mostra a arquitetura em etapas, explica por que cada serviço foi escolhido e entrega uma estimativa de custo e o código Terraform.

Projeto desenvolvido para o **DevFest**, com o **AWS Student Builder Group – SENAI CIMATEC**.

## Funcionalidades

- Fluxograma da arquitetura dividido em etapas (borda, ingestão, processamento, persistência)
- Botão de info em cada serviço explicando **por que a ferramenta foi escolhida**
- Custo estimado e score de conformidade (AWS Well-Architected)
- Simulador de tráfego e escala que recalcula o custo
- Código Terraform gerado, com botão de copiar
- Dois modelos prontos: **IoT em tempo real** e **E-commerce serverless**

## Prints

![Modelo IoT](screenshot-iot.png)
![Modelo E-commerce](screenshot-ecommerce.png)

## Como rodar

Requisito: PHP 8 ou superior.

**Com o servidor embutido do PHP:**

```bash
php -S localhost:8000
```

Acesse http://localhost:8000.

**Com XAMPP:** copie a pasta do projeto para `htdocs`, inicie o Apache e acesse `http://localhost/cloudarchitect/`.

A pasta `cache/` é criada automaticamente na primeira vez que uma arquitetura personalizada é gerada.

## Tecnologias

- PHP (lógica do servidor, sessão e cache em JSON)
- Tailwind CSS e Font Awesome (via CDN)
- JavaScript puro (modais e simulador de escala)
- Terraform (código de infraestrutura gerado)

## Limitações

Este é um **protótipo**. Os dois modelos são arquiteturas pré-definidas. Para descrições livres, a aplicação gera uma arquitetura padrão simulada, com custo e score aleatórios, sem uso de IA real ou preços reais da AWS.

## Próximos passos

- Integrar um modelo de IA para gerar arquiteturas a partir do texto
- Usar a API de preços da AWS para estimativas reais
- Exportar o diagrama como imagem

## Autores

- Mateus Paupério ([@mateuspauperio](https://github.com/mateuspauperio))
- [Nome do amigo]
