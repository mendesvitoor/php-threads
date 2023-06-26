<?php

$bufferSize = 5; // Tamanho do buffer
$producerCount = 2; // Número de produtores
$consumerCount = 2; // Número de consumidores

$buffer = array(); // Buffer compartilhado
$mutex = sem_get(1234, 1); // Semáforo para controle de acesso ao buffer
$empty = sem_get(2345, $bufferSize); // Semáforo para controlar os espaços vazios no buffer
$full = sem_get(3456, 0); // Semáforo para controlar os espaços ocupados no buffer

function producer($id)
{
    global $buffer, $bufferSize, $mutex, $empty, $full;

    for ($i = 0; $i < 10; $i++) {
        $item = "Item produzido por Produtor $id, Iteração $i";

        sem_acquire($empty); // Aguarda um espaço vazio no buffer
        sem_acquire($mutex); // Obtém acesso exclusivo ao buffer

        $buffer[] = $item; // Insere o item no buffer

        sem_release($mutex); // Libera o acesso ao buffer
        sem_release($full); // Indica que há um espaço ocupado no buffer

        echo "Produtor $id produziu: $item\n";

        usleep(rand(1000, 5000)); // Simula algum tempo de produção
    }
}

function consumer($id)
{
    global $buffer, $bufferSize, $mutex, $empty, $full;

    for ($i = 0; $i < 5; $i++) {
        sem_acquire($full); // Aguarda um espaço ocupado no buffer
        sem_acquire($mutex); // Obtém acesso exclusivo ao buffer

        $item = array_shift($buffer); // Remove o item do buffer

        sem_release($mutex); // Libera o acesso ao buffer
        sem_release($empty); // Indica que há um espaço vazio no buffer

        echo "Consumidor $id consumiu: $item\n";

        usleep(rand(1000, 5000)); // Simula algum tempo de consumo
    }
}

// Cria produtores
for ($i = 0; $i < $producerCount; $i++) {
    $pid = pcntl_fork();

    if ($pid == -1) {
        // Erro ao criar processo
        die("Erro ao criar processo.");
    } elseif ($pid == 0) {
        // Código do processo filho (produtor)
        producer($i);
        exit(); // Encerra o processo filho
    }
}

// Cria consumidores
for ($i = 0; $i < $consumerCount; $i++) {
    $pid = pcntl_fork();

    if ($pid == -1) {
        // Erro ao criar processo
        die("Erro ao criar processo.");
    } elseif ($pid == 0) {
        // Código do processo filho (consumidor)
        consumer($i);
        exit(); // Encerra o processo filho
    }
}

// Aguarda a conclusão de todos os processos filhos
while (pcntl_waitpid(-1, $status) != -1) {
    $status = pcntl_wexitstatus($status);
    echo "Processo filho com PID $status concluído.\n";
}

echo "Todos os processos filhos foram concluídos.\n";

?>
