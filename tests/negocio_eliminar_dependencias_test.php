<?php
class NegocioDeleteTestResult
{
    public int $num_rows = 1;
}

class NegocioDeleteTestStatement
{
    public int $affected_rows = 1;
    private NegocioDeleteTestConnection $connection;
    private string $sql;
    private array $values = [];

    public function __construct(NegocioDeleteTestConnection $connection, string $sql)
    {
        $this->connection = $connection;
        $this->sql = $sql;
    }

    public function bind_param(string $types, &...$values): bool
    {
        foreach ($values as $value) {
            $this->values[] = $value;
        }
        return true;
    }

    public function execute(): bool
    {
        $this->connection->executed[] = [$this->sql, $this->values[0] ?? null];
        return true;
    }

    public function close(): void {}
}

class NegocioDeleteTestConnection
{
    public array $executed = [];
    public bool $transactionStarted = false;
    public bool $committed = false;
    public bool $rolledBack = false;

    public function query(string $sql): NegocioDeleteTestResult
    {
        return new NegocioDeleteTestResult();
    }

    public function begin_transaction(): bool
    {
        $this->transactionStarted = true;
        return true;
    }

    public function prepare(string $sql): NegocioDeleteTestStatement
    {
        return new NegocioDeleteTestStatement($this, $sql);
    }

    public function commit(): bool
    {
        $this->committed = true;
        return true;
    }

    public function rollback(): bool
    {
        $this->rolledBack = true;
        return true;
    }

    public function close(): void {}
}

$testConnection = new NegocioDeleteTestConnection();
function dbConectar(): NegocioDeleteTestConnection
{
    global $testConnection;
    return $testConnection;
}

require_once __DIR__ . "/../modelos/negocios.php";

$resultado = (new Negocios())->Eliminar(812);
$esperado = [
    ["DELETE FROM horarios WHERE ID_Negocio = ?", 812],
    ["DELETE FROM trabajos WHERE ID_Negocio = ?", 812],
    ["DELETE ce FROM cupones_emitidos ce INNER JOIN promociones p ON p.ID_Promocion = ce.ID_Promocion WHERE p.ID_Negocio = ?", 812],
    ["DELETE FROM promociones WHERE ID_Negocio = ?", 812],
    ["DELETE FROM negocios WHERE ID_Negocio = ?", 812],
];

if ($resultado !== true || $testConnection->executed !== $esperado) {
    fwrite(STDERR, "FAIL: se esperaba limpiar las dependencias antes de borrar el negocio 812.\n");
    exit(1);
}

if (!$testConnection->transactionStarted || !$testConnection->committed || $testConnection->rolledBack) {
    fwrite(STDERR, "FAIL: la eliminación del negocio y sus dependencias debe confirmarse en una transacción.\n");
    exit(1);
}

echo "PASS: elimina el negocio y sus dependencias de forma atómica.\n";