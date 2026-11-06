const categories = [
    { id: 'all', name: 'Todos os Padrões', icon: 'fa-border-all' },
    { id: 'aws-cloud', name: 'AWS Cloud Patterns', icon: 'fa-cloud' },
    { id: 'creational', name: 'Padrões Criacionais (GoF)', icon: 'fa-hammer' },
    { id: 'structural', name: 'Padrões Estruturais (GoF)', icon: 'fa-sitemap' },
    { id: 'behavioral', name: 'Padrões Comportamentais (GoF)', icon: 'fa-route' }
];

// Mapeamento de Domínios Complexos -> Combinação de Padrões
const domainPresets = [
    {
        keywords: ['compra', 'compras', 'e-commerce', 'loja', 'pedidos', 'vendas', 'checkout', 'carrinho'],
        title: 'Arquitetura para Plataforma de E-commerce & Compras',
        recommendedPatterns: ['facade', 'observer', 'cqrs'],
        explanation: 'Para um sistema de compras escalável, recomendamos o uso do **Facade** (API Gateway) para centralizar a entrada dos clientes, o padrão **Observer** (SNS/SQS) para desacoplar a aprovação do pedido da logística e faturamento, e o **CQRS** para manter a alta performance da busca no catálogo.'
    },
    {
        keywords: ['iot', 'sensor', 'sensores', 'telemetria', 'dispositivos', 'tempo real', 'rastreamento'],
        title: 'Arquitetura para Ingestão e Telemetria IoT',
        recommendedPatterns: ['cqrs', 'singleton'],
        explanation: 'Para ingestão contínua de dados de sensores com alta concorrência, o padrão **CQRS** garante separação da escrita massiva e leitura de dashboards, aliado ao **Singleton/State Lock** para controle de estado dos dispositivos.'
    },
    {
        keywords: ['legado', 'integrar', 'migração', 'soap', 'xml', 'on-premise', 'sistema antigo'],
        title: 'Arquitetura para Modernização de Sistemas Legados',
        recommendedPatterns: ['adapter', 'facade'],
        explanation: 'Para conectar sistemas legados incompatíveis, utiliza-se o **Adapter** (Lambda/VTL) para traduzir payloads e o **Facade** como ponto de entrada unificado para os novos serviços em nuvem.'
    }
];

const patterns = [
    {
        id: 'cqrs',
        name: 'CQRS',
        category: 'aws-cloud',
        icon: 'fa-code-branch',
        shortDesc: 'Segregação de Responsabilidade de Comando e Consulta.',
        problem: 'Bancos de dados tradicionais sofrem contenção quando operações complexas de leitura e fluxos de alta taxa de gravação ocorrem simultaneamente no mesmo modelo de dados.',
        solution: 'Separa fisicamente as rotas de gravação (Command) e leitura (Query). No AWS, grava-se num banco otimizado para escrita rápida (DynamoDB) e sincroniza-se via Streams para um banco otimizado para buscas (OpenSearch).',
        keywords: ['leitura', 'gravação', 'banco de dados', 'contenção', 'lento', 'separar', 'query', 'command', 'dynamodb', 'opensearch', 'pesquisa', 'catálogo', 'estoque'],
        services: [
            { name: 'API Gateway', role: 'Roteamento' },
            { name: 'DynamoDB', role: 'Command Store (Write)' },
            { name: 'DynamoDB Streams', role: 'Event Sync' },
            { name: 'OpenSearch', role: 'Query Store (Read)' }
        ],
        terraform: `resource "aws_dynamodb_table" "command_store" {\n  name = "OrderCommands"\n  billing_mode = "PAY_PER_REQUEST"\n  stream_enabled = true\n  stream_view_type = "NEW_IMAGE"\n}`
    },
    {
        id: 'circuit-breaker',
        name: 'Circuit Breaker',
        category: 'aws-cloud',
        icon: 'fa-toggle-off',
        shortDesc: 'Previne falhas em cascata interrompendo chamadas a serviços indisponíveis.',
        problem: 'Se um microsserviço dependente estiver lento ou inativo, chamadas contínuas podem esgotar os recursos do sistema (threads, conexões) e causar falha total.',
        solution: 'Implementa um proxy (API Gateway ou App Mesh) ou lógica no Lambda que monitora falhas. Após um limiar, o "circuito abre" e retorna erros imediatamente, dando tempo para o serviço se recuperar.',
        keywords: ['falha', 'cair', 'fora do ar', 'indisponível', 'lento', 'cascata', 'microsserviço', 'esgotar', 'recursos', 'timeout', 'dependência', 'resiliência'],
        services: [
            { name: 'Lambda', role: 'Caller Service' },
            { name: 'ElastiCache', role: 'Estado do Circuito' },
            { name: 'API Gateway', role: 'Target Service' }
        ],
        terraform: `resource "aws_elasticache_cluster" "circuit_state" {\n  cluster_id = "circuit-breaker-state"\n  engine = "redis"\n  node_type = "cache.t4g.micro"\n  num_cache_nodes = 1\n}`
    },
    {
        id: 'facade',
        name: 'Facade',
        category: 'structural',
        icon: 'fa-building',
        shortDesc: 'Interface simplificada para um subsistema complexo.',
        problem: 'Os clientes (front-end, web, mobile) precisam interagir com dezenas de microsserviços diferentes (autenticação, faturamento, inventário), tornando a aplicação acoplada e complexa.',
        solution: 'O Amazon API Gateway atua como Facade. O cliente faz uma única chamada ao API Gateway, que orquestra e roteia as requisições para os diversos Lambdas e serviços de backend de forma transparente.',
        keywords: ['front-end', 'complexo', 'dezenas', 'várias apis', 'única', 'simplificar', 'orquestrar', 'rotear', 'interface', 'acoplado', 'api', 'gateway', 'compras', 'sistema'],
        services: [
            { name: 'Route 53', role: 'DNS' },
            { name: 'API Gateway', role: 'Facade Interface' },
            { name: 'AWS Lambda', role: 'Microservices' }
        ],
        terraform: `resource "aws_apigatewayv2_api" "facade_api" {\n  name = "SystemFacadeAPI"\n  protocol_type = "HTTP"\n}`
    },
    {
        id: 'adapter',
        name: 'Adapter',
        category: 'structural',
        icon: 'fa-plug',
        shortDesc: 'Converte uma interface em outra esperada pelo cliente.',
        problem: 'Sistemas legados on-premise enviam dados em formatos incompatíveis (ex: XML/SOAP) com os novos microsserviços Cloud (ex: JSON/REST).',
        solution: 'Uso de integrações VTL do API Gateway ou funções AWS Lambda como "Adaptadores" para traduzir payloads XML para JSON em tempo real antes de atingir o backend moderno.',
        keywords: ['legado', 'antigo', 'incompatível', 'xml', 'json', 'soap', 'rest', 'traduzir', 'converter', 'adaptar', 'on-premise', 'integração'],
        services: [
            { name: 'API Gateway', role: 'VTL Adapter' },
            { name: 'Lambda', role: 'Adapter Logic' },
            { name: 'EventBridge', role: 'Modern Bus' }
        ],
        terraform: `resource "aws_api_gateway_integration" "adapter" {\n  rest_api_id = aws_api_gateway_rest_api.api.id\n  request_templates = {\n    "application/xml" = "{ \\"json_mapped\\": \\"$input.path('$')\\" }"\n  }\n}`
    },
    {
        id: 'observer',
        name: 'Observer',
        category: 'behavioral',
        icon: 'fa-satellite-dish',
        shortDesc: 'Padrão Pub/Sub: Notifica múltiplos dependentes sobre mudanças de estado.',
        problem: 'Vários sistemas precisam reagir quando um evento ocorre (ex: pedido criado num sistema de compras). Acoplar o serviço principal a todos eles gera gargalos e reduz a escalabilidade.',
        solution: 'Implementação do padrão Pub/Sub nativo com Amazon SNS (Publisher) e SQS (Subscribers). O serviço apenas publica o evento no SNS, que distribui cópias para as filas SQS de cada domínio interessado.',
        keywords: ['notificar', 'vários', 'evento', 'assíncrono', 'pub/sub', 'sns', 'sqs', 'mensagem', 'desacoplar', 'inscritos', 'pedido', 'compras', 'pagamento'],
        services: [
            { name: 'Lambda', role: 'Publisher' },
            { name: 'Amazon SNS', role: 'Topic' },
            { name: 'Amazon SQS', role: 'Observer Queues' }
        ],
        terraform: `resource "aws_sns_topic" "order_events" {\n  name = "OrderEventsTopic"\n}\nresource "aws_sns_topic_subscription" "billing_queue" {\n  topic_arn = aws_sns_topic.order_events.arn\n  protocol  = "sqs"\n  endpoint  = aws_sqs_queue.billing.arn\n}`
    },
    {
        id: 'singleton',
        name: 'Singleton',
        category: 'creational',
        icon: 'fa-1',
        shortDesc: 'Garante que apenas uma instância exista (Controle de Concorrência).',
        problem: 'Sistemas distribuídos na nuvem podem gerar condições de corrida, onde processos simultâneos tentam gerar o mesmo ID ou processar a mesma transação ao mesmo tempo.',
        solution: 'No contexto Cloud, utiliza-se o DynamoDB com Conditional Writes (State Locking) ou o controle de concorrência Reservada do Lambda = 1 para garantir a execução única e idempotência.',
        keywords: ['duplicado', 'mesmo tempo', 'concorrência', 'corrida', 'idempotência', 'uma vez', 'única instância', 'transação', 'lock'],
        services: [
            { name: 'Lambda', role: 'Concurrency = 1' },
            { name: 'DynamoDB', role: 'Lock Table' }
        ],
        terraform: `resource "aws_lambda_function" "singleton_worker" {\n  function_name = "SingleInstanceWorker"\n  reserved_concurrent_executions = 1\n}`
    }
];