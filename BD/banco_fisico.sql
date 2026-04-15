-- Criação do banco
CREATE DATABASE smartclinic;
USE smartclinic;

-- Instituição
CREATE TABLE instituicao (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    cnpj VARCHAR(20) UNIQUE,
    logo VARCHAR(200) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(100) NOT NULL,
    nome VARCHAR(100) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    endereco VARCHAR(200) NOT NULL,
    status VARCHAR(20) NOT NULL
);

-- Médico
CREATE TABLE medico (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_instituicao_cod INT,
    cpf VARCHAR(11) UNIQUE NOT NULL,
    crm VARCHAR(20) UNIQUE NOT NULL,
    rqe VARCHAR(20) UNIQUE,
    foto VARCHAR(200),
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    senha VARCHAR(100) NOT NULL,
    especialidade VARCHAR(50),
    telefone VARCHAR(20) NOT NULL,
    endereco VARCHAR(200) NOT NULL,
    FOREIGN KEY (fk_instituicao_cod) REFERENCES instituicao(cod)
);

-- Paciente
CREATE TABLE paciente (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_instituicao_cod INT,
    cpf VARCHAR(11) UNIQUE NOT NULL,
    nome VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    email VARCHAR(100),
    senha VARCHAR(100),
    endereco VARCHAR(200),
    FOREIGN KEY (fk_instituicao_cod) REFERENCES instituicao(cod)
);

-- Solicitação 
CREATE TABLE solicitacao (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_paciente_cod INT NOT NULL,
    fk_medico_cod INT,
    tipo VARCHAR(50) NOT NULL,
    motivo TEXT NOT NULL,
    regime VARCHAR(50),
    resposta TEXT,
    status VARCHAR(20),
    FOREIGN KEY (fk_paciente_cod) REFERENCES paciente(cod),
    FOREIGN KEY (fk_medico_cod) REFERENCES medico(cod)
);

-- Prontuário 
CREATE TABLE prontuario (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_paciente_cpf VARCHAR(11) UNIQUE,
    foto VARCHAR(200),
    tipo_sanguineo VARCHAR(5),
    doencas_cronicas TEXT,
    doencas_geneticas TEXT,
    doencas_autoimunes TEXT,
    outros TEXT,
    FOREIGN KEY (fk_paciente_cpf) REFERENCES paciente(cpf)
);

-- Monitoramento 
CREATE TABLE monitoramento (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_paciente_cpf VARCHAR(11) UNIQUE,
    altura DECIMAL(5,2),
    peso DECIMAL(5,2),
    imc DECIMAL(5,2),
    FOREIGN KEY (fk_paciente_cpf) REFERENCES paciente(cpf)
);


-- Exame
CREATE TABLE exame (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_solicitacao_cod INT,
    arquivo VARCHAR(200),
    FOREIGN KEY (fk_solicitacao_cod) REFERENCES solicitacao(cod)
);

-- Agendamento
CREATE TABLE agendamento (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_solicitacao_cod INT,
    data_agendamento DATETIME,
    FOREIGN KEY (fk_solicitacao_cod) REFERENCES solicitacao(cod)
);

-- Receita
CREATE TABLE receita (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_paciente_cod INT,
    fk_medico_cod INT,
    data_receita DATE,
    tipo VARCHAR(50),
    FOREIGN KEY (fk_paciente_cod) REFERENCES paciente(cod),
    FOREIGN KEY (fk_medico_cod) REFERENCES medico(cod)
);

-- Medicamento
CREATE TABLE medicamento (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    dosagem VARCHAR(50),
    forma VARCHAR(50),
    descricao TEXT
);

-- Prescrever
CREATE TABLE prescrever (
    descricao TEXT,
    modo_uso TEXT,
    fk_receita_cod INT,
    fk_medicamento_cod INT,
    PRIMARY KEY (fk_receita_cod, fk_medicamento_cod),
    FOREIGN KEY (fk_receita_cod) REFERENCES receita(cod),
    FOREIGN KEY (fk_medicamento_cod) REFERENCES medicamento(cod)
);

-- Declaração
CREATE TABLE declaracao (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_paciente_cod INT,
    fk_medico_cod INT,
    tipo VARCHAR(50),
    motivo TEXT,
    validade DATE,
    data_hora DATETIME,
    FOREIGN KEY (fk_paciente_cod) REFERENCES paciente(cod),
    FOREIGN KEY (fk_medico_cod) REFERENCES medico(cod)
);

-- Consulta
CREATE TABLE consulta (
    cod INT PRIMARY KEY AUTO_INCREMENT,
    fk_agendamento_cod INT,
    data_consulta DATETIME,
    sintese TEXT,
    FOREIGN KEY (fk_agendamento_cod) REFERENCES agendamento(cod)
);