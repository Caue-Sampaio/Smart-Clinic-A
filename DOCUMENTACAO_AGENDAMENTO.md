# Documentação do Sistema de Agendamento - SmartClinic

## 📋 Estrutura do Fluxo de Agendamento

### Tabelas Envolvidas
- **`consulta`** - Armazena os dados principais do agendamento (data, síntese)
- **`agendamento`** - Marca qual consulta foi agendada (referência/status)
- **`solicitacao`** - Contém as informações da solicitação (motivo, tipo, paciente)
- **`paciente`** - Armazena os dados dos pacientes (nome, cod, cpf, etc)

---

## 🔄 Fluxo de Funcionamento

### 1. **Criar um Novo Agendamento**
Quando você acessa `agendamento.php` e cria um novo agendamento:

1. **Preenche os dados:**
   - **Seleciona um Paciente** (nome com código)
   - **Seleciona o Motivo** (lista de solicitações do paciente)
   - Define a **Data do Agendamento**
   - Adiciona uma **Síntese da Consulta** (opcional)

2. **O sistema faz:**
   - Insere um registro na tabela `consulta` com:
     - `fk_agendamento_cod` (preenchido depois)
     - `data_consulta` (vem do agendamento)
     - `sintese` (observações da consulta)
   
   - Insere um registro na tabela `agendamento` com:
     - `fk_solicitacao_cod` (vincula à solicitação/motivo)
     - `data_agendamento` (data do agendamento)
   
   - Atualiza a `consulta` para referenciar o `agendamento` criado

3. **Resultado:**
   - A consulta fica **armazenada e marcada como agendada**
   - O paciente é associado via solicitação

---

## 📊 Visualização de Agendamentos

### Página: `agendamento.php`
Mostra todas as **consultas que foram agendadas** com informações do paciente:

| Campo | Conteúdo |
|-------|----------|
| CÓDIGO | ID da consulta |
| PACIENTE | Nome do paciente + código |
| MOTIVO | Motivo da solicitação (truncado em 50 caracteres) |
| DATA AGENDAMENTO | Data e hora da consulta |
| SÍNTESE | Resumo da consulta (truncado em 50 caracteres) |
| AÇÕES | Editar / Deletar |

**Consultas mostradas:** Apenas aquelas que têm um registro em `agendamento`

---

## 🎯 Fluxo de Seleção no Formulário

1. **Selecionar Paciente:**
   - Dropdown mostra todos os pacientes cadastrados
   - Formato: "Nome do Paciente (Cód: X)"
   - Ao selecionar, preenche automaticamente as opções de motivo

2. **Selecionar Motivo:**
   - Dropdown mostra apenas as solicitações do paciente selecionado
   - Mostra o texto do motivo de cada solicitação
   - Atualiza dinamicamente via JavaScript

3. **Preencher Data e Síntese:**
   - Data é obrigatória
   - Síntese é opcional (para observações)

---

## 📝 Visualização de Consultas

### Página: `consulta.php`
Mostra todas as **consultas cadastradas** (com ou sem agendamento):
- Código da Consulta
- ID do Agendamento (se houver)
- Data da Consulta
- Síntese
- Opções: Deletar

**Nota:** Esta página mostra o panorama completo de todas as consultas registradas

---

## 🔑 Diferenças Principais

| Aspecto | Agendamento | Consulta |
|--------|------------|----------|
| **Campo Paciente** | Via Solicitação → Paciente | Via Agendamento → Solicitação → Paciente |
| **Visualização Paciente** | Nome + Código | Referenciado |
| **Visualização Motivo** | Direto da Solicitação | Armazenado em Consulta |
| **Campo Data** | `data_agendamento` | `data_consulta` |
| **Propósito** | Marcar e agendar | Armazenar dados da consulta |

---

## 🛠️ Classes Envolvidas

### Backend/dao/AgendamentoDAO.php
- `getAll()` - Retorna consultas agendadas com paciente_nome e motivo
- `getById($cod)` - Retorna uma consulta agendada com dados completos
- `create($data)` - Cria novo agendamento (insere em ambas as tabelas)
- `update($cod, $data)` - Atualiza agendamento
- `delete($cod)` - Remove agendamento

**Queries principais:**
```sql
SELECT c.*, ag.cod, ag.data_agendamento, s.motivo, s.fk_paciente_cod, p.nome
FROM consulta c 
LEFT JOIN agendamento ag ON c.fk_agendamento_cod = ag.cod 
LEFT JOIN solicitacao s ON ag.fk_solicitacao_cod = s.cod 
LEFT JOIN paciente p ON s.fk_paciente_cod = p.cod
WHERE ag.cod IS NOT NULL
```

### Backend/controller/PacienteController.php
- `getAll()` - Retorna todos os pacientes
- `getById($cod)` - Retorna um paciente específico

### Frontend/agendamento.php
- Carrega pacientes via `PacienteController::getAll()`
- Carrega solicitações via `SolicitacaoController::getAll()`
- JavaScript filtra solicitações por paciente
- Mostra paciente_nome e motivo na tabela

---

## ✅ Checklist de Implementação

- ✓ AgendamentoDAO atualiza para buscar paciente_nome e motivo
- ✓ Formulário agendamento.php com seleção de Paciente
- ✓ Seleção de Motivo filtra por paciente (JavaScript)
- ✓ Tabela mostra Nome do Paciente (com código)
- ✓ Tabela mostra Motivo da solicitação
- ✓ Campo de síntese mantido
- ✓ Funcionalidade Editar/Deletar mantida

---

## 🚀 Como Usar

1. Vá para **Agendamento** → **Adicionar Novo Agendamento**
2. **Selecione um Paciente** da lista
3. Aguarde a lista de motivos preencher automaticamente
4. **Escolha o Motivo** da solicitação
5. Define a **Data e Hora**
6. Adicione uma **Síntese** (observações opcionais)
7. Clique em **Salvar**
8. A consulta será armazenada e marcada como agendada

---

## 📌 Notas Importantes

- O formulário usa **cascata JavaScript**: ao selecionar um paciente, apenas suas solicitações aparecem
- A tabela exibe **nome do paciente** de forma clara com seu código
- O **motivo** é truncado em 50 caracteres na tabela (veja completo ao editar)
- Ao **deletar** um agendamento, ambas as tabelas (`agendamento` e `consulta`) são limpas
- Os dados do paciente são puxados da tabela `paciente`, não da solicitação
- A data da consulta sempre coincide com a data do agendamento

- ✓ Listagem de agendamentos mostra dados da `consulta`
- ✓ Tabela `agendamento` marca consultas agendadas
- ✓ Deletar agendamento remove de ambas as tabelas

---

## 🚀 Como Usar

1. Vá para **Agendamento** → **Adicionar Novo Agendamento**
2. Selecione uma Solicitação
3. Escolha a Data e Hora
4. Adicione uma Síntese (observações opcionais)
5. Clique em **Salvar**
6. A consulta será armazenada e marcada como agendada

---

## 📌 Notas Importantes

- A tabela `consulta` é o armazenamento principal de dados
- A tabela `agendamento` funciona como um marcador/referência
- Ao deletar um agendamento, ambos os registros são removidos
- A data da consulta sempre coincide com a data do agendamento
