<?php
session_start();

// Diretório de Cache local em JSON
$cacheDir = __DIR__ . '/cache';
if (!is_dir($cacheDir)) {
    mkdir($cacheDir, 0777, true);
}

// Arquiteturas Pré-definidas com Justificativa de Stack ('why')
$presets = [
    'iot_realtime' => [
        'title' => 'Processamento de Telemetria e Sensores IoT em Tempo Real',
        'prompt' => 'Quero um sistema para processar dados de sensores e gerar relatórios em tempo real, com alta disponibilidade e baixo custo.',
        'cost' => 148.32,
        'compliance_score' => 98,
        'stages' => [
            [
                'stage_num' => '01',
                'stage_name' => 'Borda & Segurança',
                'desc' => 'Ingestão e Auth',
                'services' => [
                    [
                        'name' => 'Route 53',
                        'type' => 'DNS Global',
                        'icon' => 'fa-network-wired',
                        'color' => 'from-indigo-600 to-blue-700',
                        'tag' => 'DNS',
                        'desc' => 'Roteamento DNS de baixa latência e checagem de saúde.',
                        'why' => 'Escolha ideal por garantir 100% de SLA, resolução de latência geográfica mínima e failover automático de edpoints.'
                    ],
                    [
                        'name' => 'Cognito',
                        'type' => 'Auth & Identity',
                        'icon' => 'fa-user-shield',
                        'color' => 'from-pink-600 to-rose-700',
                        'tag' => 'OAuth2',
                        'desc' => 'Gestão de usuários e tokens JWT de acesso.',
                        'why' => 'Elimina o custo de manter um servidor de autenticação próprio, entregando padrões OAuth 2.0 / JWT nativos com suporte a milhões de conexões.'
                    ]
                ]
            ],
            [
                'stage_num' => '02',
                'stage_name' => 'Ingestão & Broker',
                'desc' => 'Mensageria',
                'services' => [
                    [
                        'name' => 'IoT Core',
                        'type' => 'Broker MQTT',
                        'icon' => 'fa-microchip',
                        'color' => 'from-emerald-600 to-teal-700',
                        'tag' => 'MQTT',
                        'desc' => 'Conexão bidirecional e segura com dispositivos IoT.',
                        'why' => 'Suporta o protocolo leve MQTT para comunicação com sensores com baixo consumo de banda e energia nos dispositivos de campo.'
                    ],
                    [
                        'name' => 'API Gateway',
                        'type' => 'REST Router',
                        'icon' => 'fa-door-open',
                        'color' => 'from-purple-600 to-indigo-700',
                        'tag' => '10k req/s',
                        'desc' => 'Ponto de entrada HTTP e controle de carga.',
                        'why' => 'Gerencia throttling e proteção contra estouro de requisições, permitindo integrações HTTP seguras sem gerenciar infraestrutura.'
                    ]
                ]
            ],
            [
                'stage_num' => '03',
                'stage_name' => 'Processamento',
                'desc' => 'Regras e Filtros',
                'services' => [
                    [
                        'name' => 'AWS Lambda',
                        'type' => 'Event Compute',
                        'icon' => 'fa-bolt',
                        'color' => 'from-amber-500 to-orange-600',
                        'tag' => 'Serverless',
                        'desc' => 'Executa código de validação e limpeza de telemetria.',
                        'why' => 'Calcula custos estritamente por milissegundo de execução (sem custo ocioso), escalando de zero a milhares de eventos por segundo instantaneamente.'
                    ],
                    [
                        'name' => 'AWS Fargate',
                        'type' => 'Container Workers',
                        'icon' => 'fa-cubes',
                        'color' => 'from-orange-600 to-amber-700',
                        'tag' => 'Auto-Scale',
                        'desc' => 'Processamento contínuo em contêineres sem servidor.',
                        'why' => 'Ideal para fluxos contínuos e contêineres Docker que excedem o tempo limite de 15 minutos do Lambda sem precisar gerenciar instâncias EC2.'
                    ]
                ]
            ],
            [
                'stage_num' => '04',
                'stage_name' => 'Persistência & BI',
                'desc' => 'Data Lake & BI',
                'services' => [
                    [
                        'name' => 'DynamoDB',
                        'type' => 'NoSQL Database',
                        'icon' => 'fa-database',
                        'color' => 'from-blue-600 to-cyan-600',
                        'tag' => '<10ms',
                        'desc' => 'Banco NoSQL ultra-rápido para séries temporais.',
                        'why' => 'Garante latência consistente de um único dígito de milissegundo em qualquer escala de escrita de dados de sensores.'
                    ],
                    [
                        'name' => 'Amazon S3',
                        'type' => 'Data Lake Bucket',
                        'icon' => 'fa-box-archive',
                        'color' => 'from-green-600 to-emerald-700',
                        'tag' => 'Parquet',
                        'desc' => 'Armazenamento de longo prazo em formato bruto.',
                        'why' => 'Entrega 99,999999999% (11 9s) de durabilidade de dados a um custo extremamente reduzido por Gigabyte armazenado.'
                    ],
                    [
                        'name' => 'QuickSight',
                        'type' => 'Dashboards BI',
                        'icon' => 'fa-chart-pie',
                        'color' => 'from-violet-600 to-purple-700',
                        'tag' => 'Realtime',
                        'desc' => 'Visualização de dados e dashboards interativos.',
                        'why' => 'Ferramenta Serverless de BI nativa da AWS com cobrança por sessão de usuário e motor SPICE para renderização instantânea.'
                    ]
                ]
            ]
        ],
        'security' => [
            ['title' => 'IAM Role com menor privilégio', 'status' => 'Conforme'],
            ['title' => 'VPC com Subredes Privadas', 'status' => 'Conforme'],
            ['title' => 'AWS WAF - Proteção OWASP', 'status' => 'Conforme']
        ],
        'terraform' => "# Terraform - AWS SGB SENAI CIMATEC\nprovider \"aws\" {\n  region = \"us-east-1\"\n}\n\nresource \"aws_dynamodb_table\" \"sensor_telemetry\" {\n  name         = \"SensorTelemetry_CIMATEC\"\n  billing_mode = \"PAY_PER_REQUEST\"\n  hash_key     = \"device_id\"\n  range_key    = \"timestamp\"\n\n  attribute {\n    name = \"device_id\"\n    type = \"S\"\n  }\n  attribute {\n    name = \"timestamp\"\n    type = \"N\"\n  }\n}"
    ],
    'ecommerce' => [
        'title' => 'Plataforma E-commerce Escalável Serverless',
        'prompt' => 'Necessito de uma arquitetura para e-commerce com alta capacidade para picos de acesso e autenticação de clientes.',
        'cost' => 285.50,
        'compliance_score' => 96,
        'stages' => [
            [
                'stage_num' => '01',
                'stage_name' => 'Borda & Auth',
                'desc' => 'CDN & Login',
                'services' => [
                    [
                        'name' => 'CloudFront',
                        'type' => 'Global CDN',
                        'icon' => 'fa-globe',
                        'color' => 'from-indigo-600 to-blue-700',
                        'tag' => 'Edge Cache',
                        'desc' => 'Distribuição de conteúdo e cache global.',
                        'why' => 'Armazena em cache os produtos e imagens na borda mais próxima do comprador, reduzindo a carga nos servidores de origem em até 90%.'
                    ],
                    [
                        'name' => 'AWS Cognito',
                        'type' => 'Auth de Clientes',
                        'icon' => 'fa-user-lock',
                        'color' => 'from-pink-600 to-rose-700',
                        'tag' => 'JWT',
                        'desc' => 'Autenticação e gestão de sessão dos compradores.',
                        'why' => 'Oferece fluxo de login, cadastro, recuperação de senha e MFA pronto e em conformidade com LGPD/GDPR sem codificação de backend.'
                    ]
                ]
            ],
            [
                'stage_num' => '02',
                'stage_name' => 'Roteamento & Filas',
                'desc' => 'Absorção de Picos',
                'services' => [
                    [
                        'name' => 'API Gateway',
                        'type' => 'API Router',
                        'icon' => 'fa-door-open',
                        'color' => 'from-purple-600 to-indigo-700',
                        'tag' => 'REST',
                        'desc' => 'Roteamento seguro das chamadas de checkout.',
                        'why' => 'Roteia requisições HTTPS e aplica chaves de API e autorização JWT antes das chamadas atingirem a lógica de negócio.'
                    ],
                    [
                        'name' => 'Amazon SQS',
                        'type' => 'Queue Service',
                        'icon' => 'fa-list-check',
                        'color' => 'from-red-600 to-orange-700',
                        'tag' => 'ZeroLoss',
                        'desc' => 'Fila de pedidos desacoplada durante grandes liquidações.',
                        'why' => 'Garante a absorção de milhares de compras por segundo na Black Friday, enfileirando pedidos para processamento assíncrono sem queda do sistema.'
                    ]
                ]
            ],
            [
                'stage_num' => '03',
                'stage_name' => 'Processamento',
                'desc' => 'Checkout Serverless',
                'services' => [
                    [
                        'name' => 'AWS Lambda',
                        'type' => 'Checkout Handler',
                        'icon' => 'fa-bolt',
                        'color' => 'from-amber-500 to-orange-600',
                        'tag' => 'Serverless',
                        'desc' => 'Processa pagamentos e pedidos em milissegundos.',
                        'why' => 'Garante custo zero quando a loja não tem acessos e ajusta automaticamente o poder computacional para picos de milhares de vendas simultâneas.'
                    ]
                ]
            ],
            [
                'stage_num' => '04',
                'stage_name' => 'Banco de Dados',
                'desc' => 'Catálogo & Vendas',
                'services' => [
                    [
                        'name' => 'DynamoDB',
                        'type' => 'Product Catalog',
                        'icon' => 'fa-database',
                        'color' => 'from-blue-600 to-cyan-600',
                        'tag' => 'Auto Scale',
                        'desc' => 'Banco NoSQL de alta performance para o catálogo.',
                        'why' => 'Oferece auto-scaling sob demanda para suportar pesquisas massivas no catálogo sem sobrecarregar bancos relacionais tradicionais.'
                    ]
                ]
            ]
        ],
        'security' => [
            ['title' => 'AWS Shield - Anti-DDoS', 'status' => 'Conforme'],
            ['title' => 'Criptografia TLS 1.3', 'status' => 'Conforme'],
            ['title' => 'AWS KMS - Chaves Gerenciadas', 'status' => 'Conforme']
        ],
        'terraform' => "# Terraform E-Commerce Serverless SGB CIMATEC\nresource \"aws_sqs_queue\" \"orders_queue\" {\n  name = \"cimatec-orders-queue\"\n}"
    ]
];

$currentKey = $_GET['preset'] ?? 'iot_realtime';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userPrompt = trim($_POST['prompt'] ?? '');
    if (!empty($userPrompt)) {
        $hashKey = 'custom_' . md5($userPrompt);
        $customArch = [
            'title' => 'Arquitetura Personalizada pelo CloudArchitect AI',
            'prompt' => $userPrompt,
            'cost' => rand(120, 290) + 0.45,
            'compliance_score' => rand(93, 99),
            'stages' => [
                [
                    'stage_num' => '01',
                    'stage_name' => 'Entrada & Roteamento',
                    'desc' => 'Conexão',
                    'services' => [
                        [
                            'name' => 'Route 53',
                            'type' => 'DNS Global',
                            'icon' => 'fa-network-wired',
                            'color' => 'from-indigo-600 to-blue-700',
                            'tag' => 'DNS',
                            'desc' => 'Roteamento de entrada.',
                            'why' => 'Resolução de domínio de alta velocidade com suporte a checagem de saúde.'
                        ],
                        [
                            'name' => 'API Gateway',
                            'type' => 'API REST',
                            'icon' => 'fa-door-open',
                            'color' => 'from-purple-600 to-indigo-700',
                            'tag' => 'Ingestão',
                            'desc' => 'Gerenciamento de chamadas.',
                            'why' => 'Ponto de entrada seguro e gerenciado para APIs REST e WebSockets.'
                        ]
                    ]
                ],
                [
                    'stage_num' => '02',
                    'stage_name' => 'Processamento',
                    'desc' => 'Execução',
                    'services' => [
                        [
                            'name' => 'AWS Lambda',
                            'type' => 'Serverless Compute',
                            'icon' => 'fa-bolt',
                            'color' => 'from-amber-500 to-orange-600',
                            'tag' => 'Compute',
                            'desc' => 'Lógica de execução.',
                            'why' => 'Computação orientada a eventos sem necessidade de provisionar ou gerenciar servidores.'
                        ]
                    ]
                ],
                [
                    'stage_num' => '03',
                    'stage_name' => 'Armazenamento',
                    'desc' => 'Banco de dados',
                    'services' => [
                        [
                            'name' => 'DynamoDB',
                            'type' => 'NoSQL Database',
                            'icon' => 'fa-database',
                            'color' => 'from-blue-600 to-cyan-600',
                            'tag' => 'NoSQL',
                            'desc' => 'Gravação de dados de aplicação.',
                            'why' => 'Banco de dados de chave-valor e documentos de alto rendimento e baixa latência.'
                        ]
                    ]
                ],
                [
                    'stage_num' => '04',
                    'stage_name' => 'Data Lake',
                    'desc' => 'Histórico & Logs',
                    'services' => [
                        [
                            'name' => 'Amazon S3',
                            'type' => 'Storage Bucket',
                            'icon' => 'fa-box-archive',
                            'color' => 'from-green-600 to-emerald-700',
                            'tag' => 'Storage',
                            'desc' => 'Arquivos e logs brutos.',
                            'why' => 'Armazenamento de objetos seguro, durável e com escala ilimitada para logs e backups.'
                        ]
                    ]
                ]
            ],
            'security' => [
                ['title' => 'Políticas IAM restritivas', 'status' => 'Conforme'],
                ['title' => 'Logs no Amazon CloudWatch', 'status' => 'Conforme']
            ],
            'terraform' => "# Arquitetura gerada para: " . addslashes($userPrompt) . "\nresource \"aws_s3_bucket\" \"app_bucket\" {\n  bucket = \"cimatec-gen-app-" . time() . "\"\n}"
        ];
        
        file_put_contents($cacheDir . '/' . $hashKey . '.json', json_encode($customArch, JSON_PRETTY_PRINT));
        $_SESSION['cache'][$hashKey] = $customArch;
        $currentKey = $hashKey;
    }
}

if (isset($presets[$currentKey])) {
    $activeArch = $presets[$currentKey];
} elseif (isset($_SESSION['cache'][$currentKey])) {
    $activeArch = $_SESSION['cache'][$currentKey];
} elseif (file_exists($cacheDir . '/' . $currentKey . '.json')) {
    $activeArch = json_decode(file_get_contents($cacheDir . '/' . $currentKey . '.json'), true);
} else {
    $activeArch = $presets['iot_realtime'];
}

$totalServices = 0;
foreach ($activeArch['stages'] as $stg) {
    $totalServices += count($stg['services']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CloudArchitect AI | AWS Student Builder Group SENAI CIMATEC</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome para Ícones -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        sgb: {
                            dark: '#080C14',
                            surface: '#0F1624',
                            card: '#161F33',
                            border: '#222F47',
                            orange: '#FF9900',
                            cyan: '#00F0FF',
                            navy: '#002B49'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .glow-orange { box-shadow: 0 0 15px rgba(255, 153, 0, 0.25); }
    </style>
</head>
<body class="bg-sgb-dark text-slate-100 font-sans min-h-screen flex flex-col antialiased">

    <!-- CABEÇALHO -->
    <header class="bg-sgb-surface/90 border-b border-sgb-border shrink-0 py-2.5 px-6 sticky top-0 z-50 backdrop-blur-md">
        <div class="w-full max-w-[1600px] mx-auto flex items-center justify-between">
            
            <!-- MARCA E LOGO SGB SENAI CIMATEC -->
            <div class="flex items-center space-x-3">
                <div class="flex items-center gap-2.5 bg-sgb-dark px-3 py-1.5 rounded-xl border border-sgb-border shadow-md">
                    <svg class="w-7 h-7 text-white" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <rect x="25" y="25" width="50" height="50" rx="3" stroke="white" stroke-width="8" fill="none"/>
                        <rect x="33" y="10" width="8" height="15" fill="white" rx="1"/>
                        <rect x="46" y="10" width="8" height="15" fill="white" rx="1"/>
                        <rect x="59" y="10" width="8" height="15" fill="white" rx="1"/>
                        <rect x="33" y="75" width="8" height="15" fill="white" rx="1"/>
                        <rect x="46" y="75" width="8" height="15" fill="white" rx="1"/>
                        <rect x="59" y="75" width="8" height="15" fill="white" rx="1"/>
                        <rect x="10" y="33" width="15" height="8" fill="white" rx="1"/>
                        <rect x="10" y="46" width="15" height="8" fill="white" rx="1"/>
                        <rect x="10" y="59" width="15" height="8" fill="white" rx="1"/>
                        <rect x="75" y="33" width="15" height="8" fill="white" rx="1"/>
                        <rect x="75" y="46" width="15" height="8" fill="white" rx="1"/>
                        <rect x="75" y="59" width="15" height="8" fill="white" rx="1"/>
                    </svg>

                    <div class="border-l border-slate-700 pl-2.5">
                        <span class="text-[11px] font-black text-white tracking-wider block leading-none">AWS Student Builder Group</span>
                        <span class="text-[9px] font-bold text-indigo-300 block leading-tight mt-0.5">SENAI CIMATEC</span>
                    </div>
                </div>

                <div class="border-l border-sgb-border pl-3">
                    <h1 class="text-base font-black tracking-tight text-white leading-none">CloudArchitect <span class="text-sgb-orange">AI</span></h1>
                    <p class="text-[10px] text-slate-400 mt-0.5">Gerador Intuitivo de Arquiteturas para Nuvem</p>
                </div>
            </div>

            <!-- LINKS SOCIAIS -->
            <div class="flex items-center space-x-2">
                <a href="https://www.instagram.com/awssbgsenaicimatec/" target="_blank" rel="noopener noreferrer" 
                   class="bg-sgb-card hover:bg-pink-600/20 text-slate-300 hover:text-pink-400 px-3 py-1.5 rounded-lg border border-sgb-border hover:border-pink-500/40 transition-all flex items-center gap-1.5 text-xs font-semibold">
                    <i class="fa-brands fa-instagram text-pink-500"></i>
                    <span>Instagram</span>
                </a>

                <a href="https://www.linkedin.com/company/awssbgcimatec/posts/?feedView=all" target="_blank" rel="noopener noreferrer" 
                   class="bg-sgb-card hover:bg-sky-600/20 text-slate-300 hover:text-sky-400 px-3 py-1.5 rounded-lg border border-sgb-border hover:border-sky-500/40 transition-all flex items-center gap-1.5 text-xs font-semibold">
                    <i class="fa-brands fa-linkedin text-sky-400"></i>
                    <span>LinkedIn</span>
                </a>

                <a href="https://www.awsclubsenai.com.br/join" target="_blank" rel="noopener noreferrer" 
                   class="bg-gradient-to-r from-sgb-orange to-amber-500 hover:from-amber-500 hover:to-sgb-orange text-slate-950 font-black px-3.5 py-1.5 rounded-lg transition-all shadow-md flex items-center gap-1.5 text-xs">
                    <i class="fa-solid fa-users"></i>
                    <span>Junte-se ao SGB</span>
                    <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                </a>
            </div>
        </div>
    </header>

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="w-full max-w-[1600px] mx-auto px-6 py-4 flex-grow grid grid-cols-12 gap-5">

        <!-- PAINEL DE ENTRADA (COLUNA ESQUERDA - 4 COLS) -->
        <section class="col-span-4 space-y-4">
            <div class="bg-sgb-card border border-sgb-border rounded-xl p-4 relative overflow-hidden space-y-4">
                <div class="absolute top-0 left-0 w-1.5 h-full bg-sgb-orange"></div>
                
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="bg-sgb-orange/20 text-sgb-orange font-black text-xs h-5 w-5 rounded-md flex items-center justify-center border border-sgb-orange/30">1</span>
                        <h2 class="font-bold text-white text-sm">Entrada da Ideia</h2>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-3">Descreva os requisitos do projeto ou selecione uma arquitetura pronta do SGB CIMATEC.</p>

                    <!-- FORMULÁRIO -->
                    <form method="POST" action="index.php" class="space-y-3">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-300 mb-1">Descrição do Projeto:</label>
                            <textarea name="prompt" rows="4" class="w-full bg-sgb-dark border border-sgb-border rounded-lg p-2.5 text-xs text-slate-100 placeholder-slate-500 focus:ring-1 focus:ring-sgb-orange outline-none transition-all resize-none" placeholder="Ex: Preciso de uma API para receber dados de telemetria de tratores, processar e gerar relatórios NoSQL..."><?php echo htmlspecialchars($activeArch['prompt']); ?></textarea>
                        </div>

                        <button type="submit" class="w-full bg-gradient-to-r from-sgb-orange to-amber-500 hover:from-amber-500 hover:to-sgb-orange text-slate-950 font-black py-2.5 px-3 rounded-lg text-xs transition-all flex items-center justify-center gap-2 shadow-md">
                            <i class="fa-solid fa-wand-magic-sparkles text-xs"></i>
                            <span>Gerar Arquitetura AWS Completa</span>
                        </button>
                    </form>
                </div>

                <!-- MODELOS RECOMENDADOS (SGB PRESETS) -->
                <div class="pt-3 border-t border-sgb-border space-y-2">
                    <span class="text-[11px] font-bold text-slate-300 block">Modelos do SGB SENAI CIMATEC:</span>
                    <div class="space-y-2">
                        <a href="?preset=iot_realtime" class="block p-2.5 rounded-lg border <?php echo $currentKey === 'iot_realtime' ? 'border-sgb-orange bg-sgb-orange/10 text-amber-300 glow-orange' : 'border-sgb-border bg-sgb-dark/60 text-slate-400 hover:border-slate-700'; ?> text-xs transition-all">
                            <div class="font-bold flex items-center justify-between text-[11px]">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-microchip text-sgb-orange"></i> IoT Telemetria em Tempo Real
                                </span>
                                <i class="fa-solid fa-chevron-right text-[9px] opacity-60"></i>
                            </div>
                        </a>

                        <a href="?preset=ecommerce" class="block p-2.5 rounded-lg border <?php echo $currentKey === 'ecommerce' ? 'border-sgb-orange bg-sgb-orange/10 text-amber-300 glow-orange' : 'border-sgb-border bg-sgb-dark/60 text-slate-400 hover:border-slate-700'; ?> text-xs transition-all">
                            <div class="font-bold flex items-center justify-between text-[11px]">
                                <span class="flex items-center gap-2">
                                    <i class="fa-solid fa-cart-shopping text-sgb-cyan"></i> E-commerce Serverless
                                </span>
                                <i class="fa-solid fa-chevron-right text-[9px] opacity-60"></i>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- PAINEL DE SAÍDA E FLUXOGRAMA (COLUNA DIREITA - 8 COLS) -->
        <section class="col-span-8 space-y-4">
            <div class="bg-sgb-card border border-sgb-border rounded-xl p-4 relative overflow-hidden space-y-4">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-sgb-orange via-sgb-cyan to-indigo-500"></div>

                <!-- BARRA SUPERIOR DA SAÍDA -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="bg-sgb-cyan/20 text-sgb-cyan font-black text-xs h-5 w-5 rounded-md flex items-center justify-center border border-sgb-cyan/30">2</span>
                        <h2 class="font-bold text-white text-sm">Saída da Arquitetura Gerada</h2>
                        <span class="text-[11px] text-slate-400 ml-2 truncate font-medium">(<?php echo htmlspecialchars($activeArch['title']); ?>)</span>
                    </div>
                    <span class="bg-emerald-500/10 text-emerald-400 text-[10px] px-2.5 py-0.5 rounded-full border border-emerald-500/30 font-bold flex items-center gap-1">
                        <i class="fa-solid fa-circle-check text-[8px]"></i> AWS Well-Architected
                    </span>
                </div>

                <!-- MÉTRICAS DO PROJETO -->
                <div class="grid grid-cols-4 gap-3">
                    <div class="bg-sgb-dark p-2.5 rounded-lg border border-sgb-border">
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block">Custo Estimado</span>
                        <div class="text-sm font-black text-white mt-0.5">
                            US$ <span id="totalCostDisplay"><?php echo number_format($activeArch['cost'], 2, ',', '.'); ?></span>
                        </div>
                    </div>

                    <div class="bg-sgb-dark p-2.5 rounded-lg border border-sgb-border">
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block">Score Compliance</span>
                        <div class="text-sm font-black text-sgb-cyan mt-0.5">
                            <?php echo $activeArch['compliance_score']; ?>%
                        </div>
                    </div>

                    <div class="bg-sgb-dark p-2.5 rounded-lg border border-sgb-border">
                        <span class="text-[9px] uppercase font-bold text-slate-400 tracking-wider block">Serviços AWS</span>
                        <div class="text-sm font-black text-sgb-orange mt-0.5">
                            <?php echo $totalServices; ?> Nós Integrados
                        </div>
                    </div>

                    <div class="bg-sgb-dark p-2.5 rounded-lg border border-sgb-border flex items-center justify-center">
                        <button onclick="openIacModal()" class="w-full bg-sgb-surface hover:bg-slate-800 text-sgb-orange border border-sgb-orange/40 font-bold py-1.5 px-2 rounded text-[11px] transition-all flex items-center justify-center gap-1.5">
                            <i class="fa-solid fa-code text-[10px]"></i>
                            <span>Ver Código Terraform</span>
                        </button>
                    </div>
                </div>

                <!-- PIPELINE VISUAL DA ARQUITETURA -->
                <div class="bg-sgb-dark border border-sgb-border rounded-lg p-3 space-y-3">
                    <div class="flex items-center justify-between text-[10px] text-slate-400 pb-2 border-b border-sgb-border">
                        <span class="font-bold text-slate-200 flex items-center gap-1.5">
                            <i class="fa-solid fa-diagram-project text-sgb-orange"></i> Fluxograma da Arquitetura (Pipeline de Dados Lógico)
                        </span>
                        <span class="text-sgb-cyan font-mono text-[9px]"><i class="fa-solid fa-arrow-right-long mr-1"></i>Clique no ícone "Info" ou no card para detalhes</span>
                    </div>

                    <!-- GRID DAS ETAPAS DO FLUXOGRAMA COM BOTÃO INFO EM CADA CARD -->
                    <div class="grid grid-cols-4 gap-3">
                        <?php foreach ($activeArch['stages'] as $sIndex =>$stage): ?>
                            <div class="bg-sgb-surface border border-sgb-border rounded-lg p-2.5 space-y-2">
                                <div class="border-b border-sgb-border/60 pb-1 flex items-center justify-between">
                                    <span class="text-[9px] font-mono font-bold text-sgb-cyan bg-slate-800 px-1.5 py-0.5 rounded">
                                        E<?php echo $stage['stage_num']; ?>
                                    </span>
                                    <h3 class="text-[10px] font-bold text-slate-200 truncate uppercase">
                                        <?php echo htmlspecialchars($stage['stage_name']); ?>
                                    </h3>
                                </div>

                                <div class="space-y-2">
                                    <?php foreach ($stage['services'] as$srv): ?>
                                        <div onclick="showServiceDetails('<?php echo addslashes($srv['name']); ?>', '<?php echo addslashes($srv['type']); ?>', '<?php echo addslashes($srv['desc']); ?>', '<?php echo addslashes($srv['why'] ?? 'Ferramenta selecionada por alta eficiência, segurança gerenciada e nativa integração AWS.'); ?>')"
                                             class="bg-sgb-dark border border-sgb-border hover:border-sgb-orange p-2 rounded-md cursor-pointer transition-all hover:scale-[1.02] flex items-center justify-between group">
                                            
                                            <!-- Ícone + Título do Serviço -->
                                            <div class="flex items-center space-x-2 overflow-hidden">
                                                <div class="bg-gradient-to-br <?php echo $srv['color']; ?> h-7 w-7 min-w-[28px] rounded flex items-center justify-center text-white text-[11px] shadow-sm">
                                                    <i class="fa-solid <?php echo $srv['icon']; ?>"></i>
                                                </div>
                                                <div class="overflow-hidden">
                                                    <h4 class="text-[11px] font-bold text-slate-100 group-hover:text-sgb-orange transition-colors truncate"><?php echo $srv['name']; ?></h4>
                                                    <p class="text-[9px] text-slate-400 truncate"><?php echo $srv['type']; ?></p>
                                                </div>
                                            </div>

                                            <!-- BOTÃO INFO PARA EXPLICAR A ESCOLHA DA FERRAMENTA -->
                                            <button type="button" 
                                                    title="Ver justificativa de escolha da ferramenta"
                                                    class="text-slate-500 group-hover:text-sgb-orange hover:bg-sgb-orange/20 p-1 rounded-md transition-colors text-xs shrink-0 ml-1"
                                                    onclick="event.stopPropagation(); showServiceDetails('<?php echo addslashes($srv['name']); ?>', '<?php echo addslashes($srv['type']); ?>', '<?php echo addslashes($srv['desc']); ?>', '<?php echo addslashes($srv['why'] ?? 'Ferramenta selecionada por alta eficiência e segurança gerenciada AWS.'); ?>')">
                                                <i class="fa-solid fa-circle-info"></i>
                                            </button>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- SIMULADOR E REQUISITOS DE SEGURANÇA -->
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-sgb-dark border border-sgb-border rounded-lg p-2.5">
                        <div class="flex justify-between text-[11px] text-slate-400 mb-1">
                            <span>Simulador de Tráfego e Escala:</span>
                            <span id="scaleLabel" class="text-sgb-orange font-bold">1x (Uso Padrão)</span>
                        </div>
                        <input type="range" id="scaleSlider" min="0.5" max="5" step="0.5" value="1" oninput="updateCostScale(this.value, <?php echo $activeArch['cost']; ?>)" class="w-full h-1 bg-slate-800 rounded-lg appearance-none cursor-pointer accent-sgb-orange">
                    </div>

                    <div class="bg-sgb-dark border border-sgb-border rounded-lg p-2.5">
                        <span class="text-[11px] font-bold text-slate-200 block mb-1">Pilares de Conformidade AWS:</span>
                        <div class="flex items-center justify-between text-[10px] text-slate-400">
                            <?php foreach ($activeArch['security'] as$sec): ?>
                                <span class="flex items-center gap-1">
                                    <i class="fa-solid fa-circle-check text-emerald-400"></i>
                                    <span class="truncate"><?php echo explode(' - ', $sec['title'])[0]; ?></span>
                                </span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </main>

    <!-- MODAL DETALHES DO SERVIÇO COM EXPLICAÇÃO TÉCNICA DA ESCOLHA -->
    <div id="serviceModal" class="fixed inset-0 bg-sgb-dark/80 backdrop-blur-sm hidden items-center justify-center p-4 z-50" onclick="closeModalOnOverlay(event, 'serviceModal')">
        <div class="bg-sgb-card border border-sgb-border rounded-2xl max-w-lg w-full p-5 shadow-2xl space-y-4">
            <!-- Cabeçalho do Modal -->
            <div class="flex items-center justify-between border-b border-sgb-border pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="bg-sgb-orange/20 text-sgb-orange p-2 rounded-xl border border-sgb-orange/30">
                        <i class="fa-solid fa-cube text-base"></i>
                    </div>
                    <div>
                        <h3 id="modalTitle" class="font-bold text-white text-sm">Serviço AWS</h3>
                        <p id="modalType" class="text-[11px] text-sgb-cyan font-mono">Tipo</p>
                    </div>
                </div>
                <button onclick="closeModal('serviceModal')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>

            <!-- Corpo da Explicação -->
            <div class="space-y-3">
                <div>
                    <span class="text-[10px] font-bold uppercase text-slate-400 tracking-wider block mb-1">Função na Arquitetura:</span>
                    <p id="modalDesc" class="text-xs text-slate-300 leading-relaxed"></p>
                </div>

                <div class="bg-sgb-dark border border-sgb-border p-3.5 rounded-xl space-y-1">
                    <span class="text-[10px] font-bold uppercase text-sgb-orange tracking-wider flex items-center gap-1.5 mb-1">
                        <i class="fa-solid fa-circle-info text-sgb-orange"></i> Por que escolhemos esta ferramenta?
                    </span>
                    <p id="modalWhy" class="text-xs text-slate-200 leading-relaxed"></p>
                </div>
            </div>

            <div class="pt-1 flex justify-end">
                <button onclick="closeModal('serviceModal')" class="bg-sgb-surface hover:bg-slate-800 text-xs text-slate-200 font-bold px-4 py-2 rounded-lg border border-slate-700">
                    Fechar
                </button>
            </div>
        </div>
    </div>

    <!-- MODAL CÓDIGO TERRAFORM (IaC) -->
    <div id="iacModal" class="fixed inset-0 bg-sgb-dark/80 backdrop-blur-sm hidden items-center justify-center p-4 z-50" onclick="closeModalOnOverlay(event, 'iacModal')">
        <div class="bg-sgb-card border border-sgb-border rounded-2xl max-w-xl w-full p-5 shadow-2xl space-y-3">
            <div class="flex items-center justify-between border-b border-sgb-border pb-2">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-terminal text-sgb-orange text-base"></i>
                    <h3 class="font-bold text-white text-sm">Código Terraform Automático (IaC)</h3>
                </div>
                <button onclick="closeModal('iacModal')" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-base"></i>
                </button>
            </div>
            <pre class="bg-sgb-dark p-3 rounded-lg text-emerald-400 text-[11px] font-mono overflow-x-auto max-h-60 border border-sgb-border"><?php echo htmlspecialchars($activeArch['terraform']); ?></pre>
            <div class="flex items-center justify-between pt-1">
                <span class="text-[10px] text-slate-500">HashiCorp HCL Standard</span>
                <button onclick="copyIacCode()" class="bg-sgb-orange hover:bg-amber-500 text-slate-950 font-bold px-3 py-1.5 rounded-lg text-xs flex items-center gap-1.5 shadow-md">
                    <i class="fa-solid fa-copy"></i>
                    <span>Copiar Código</span>
                </button>
            </div>
        </div>
    </div>

    <!-- JAVASCRIPT INTERATIVO -->
    <script>
        function showServiceDetails(name, type, desc, why) {
            document.getElementById('modalTitle').innerText = name;
            document.getElementById('modalType').innerText = type;
            document.getElementById('modalDesc').innerText = desc;
            document.getElementById('modalWhy').innerText = why || 'Selecionado por alinhar-se aos pilares de eficiência de performance e segurança gerenciada da AWS.';
            document.getElementById('serviceModal').classList.remove('hidden');
            document.getElementById('serviceModal').classList.add('flex');
        }

        function openIacModal() {
            document.getElementById('iacModal').classList.remove('hidden');
            document.getElementById('iacModal').classList.add('flex');
        }

        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
        }

        function closeModalOnOverlay(e, id) {
            if (e.target.id === id) closeModal(id);
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal('serviceModal');
                closeModal('iacModal');
            }
        });

        function updateCostScale(scale, baseCost) {
            const calculatedCost = (baseCost * scale).toFixed(2);
            document.getElementById('totalCostDisplay').innerText = new Intl.NumberFormat('pt-BR', { minimumFractionDigits: 2 }).format(calculatedCost);
            document.getElementById('scaleLabel').innerText = scale + 'x (' + (scale > 1 ? 'Elevado' : 'Padrão') + ')';
        }

        function copyIacCode() {
            const code = `<?php echo addslashes($activeArch['terraform']); ?>`;
            navigator.clipboard.writeText(code);
            alert('Código Terraform copiado para a área de transferência!');
        }
    </script>
</body>
</html>