# php-threads

O código é dividido em três partes principais: a função do produtor, a função do consumidor e a parte de criação dos processos filhos.

Função do Produtor:
A função producer($id) é responsável por realizar a produção dos itens. Ela recebe como parâmetro o ID do produtor, que é usado apenas para identificação.
Dentro do loop for, o produtor produz 10 itens. A cada iteração, um item é criado e armazenado na variável $item.
Em seguida, os semáforos sem_acquire($empty) e sem_acquire($mutex) são usados para garantir que haja um espaço vazio no buffer e para obter acesso exclusivo ao buffer.
O item é adicionado ao buffer com $buffer[] = $item, e os semáforos sem_release($mutex) e sem_release($full) são usados para liberar o acesso ao buffer e indicar que há um espaço ocupado no buffer.
Por fim, uma mensagem é exibida indicando que o produtor produziu o item e há uma pausa simulada usando usleep(rand(1000, 5000)).

Função do Consumidor:
A função consumer($id) é responsável por realizar o consumo dos itens. Ela recebe como parâmetro o ID do consumidor, que é usado apenas para identificação.
Dentro do loop for, o consumidor consome 5 itens. A cada iteração, os semáforos sem_acquire($full) e sem_acquire($mutex) são usados para garantir que haja um espaço ocupado no buffer e para obter acesso exclusivo ao buffer.
O primeiro item do buffer é removido usando array_shift($buffer), e os semáforos sem_release($mutex) e sem_release($empty) são usados para liberar o acesso ao buffer e indicar que há um espaço vazio no buffer.
Por fim, uma mensagem é exibida indicando que o consumidor consumiu o item e há uma pausa simulada usando usleep(rand(1000, 5000)).

Criação dos Processos Filhos:
A parte de criação dos processos filhos ocorre em duas etapas: criação dos produtores e criação dos consumidores.
No loop for de criação dos produtores, para cada produtor, um novo processo filho é criado usando pcntl_fork(). Se ocorrer um erro na criação do processo, o código exibe uma mensagem de erro e encerra.
No código do processo filho do produtor, o ID do produtor é passado para a função producer($id), e o processo filho é encerrado usando exit().
O mesmo processo é repetido para a criação dos consumidores.
Após a criação de todos os processos filhos, o processo pai aguarda a conclusão de todos os processos filhos usando o loop while (pcntl_waitpid(-1, $status) != -1). A função pcntl_wexitstatus($status) é usada para obter o status de saída de cada processo filho e exibir uma mensagem indicando que o processo filho foi concluído.
Por fim, uma mensagem é exibida indicando que todos os processos filhos foram concluídos.

O uso de semáforos (sem_get, sem_acquire, sem_release) permite o controle de acesso ao buffer compartilhado entre os processos, evitando condições de corrida e garantindo que os produtores e consumidores sincronizem adequadamente suas ações.
