<?php
class CuponDeleteTestResult
{
    public int $num_rows = 1;
}

class CuponDeleteTestStatement
{
    public int $affected_rows = 1;
    private CuponDeleteTestConnection $connection;
    private string $sql;
    private array $values = [];

    public function __construct(CuponDeleteTestConnection $connection, string $sql)
    {
        $this->connection = $connection;
        $this->sql = $sql;
    }

    public function bind_param(string $types, &...$values): bool
    {
        foreach ($values as &$value) {
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

class CuponDeleteTestConnection
{
    public array $executed = [];
    public bool $transactionStarted = false;
    public bool $committed = false;
    public bool $rolledBack = false;

    public function query(string $sql): CuponDeleteTestResult
    {
        return new CuponDeleteTestResult();
    }

    public function begin_transaction(): bool
    {
        $this->transactionStarted = true;
        return true;
    }

    public function prepare(string $sql): CuponDeleteTestStatement
    {
        return new CuponDeleteTestStatement($this, $sql);
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

$testConnection = new CuponDeleteTestConnection();
function dbConectar(): CuponDeleteTestConnection
{
    global $testConnection;
    return $testConnection;
}

require_once __DIR__ . "/../modelos/cupones.php";

if (!method_exists(Cupones::class, "EliminarPromocion")) {
    fwrite(STDERR, "FAIL: Cupones debe exponer EliminarPromocion().\n");
    exit(1);
}

$resultado = (new Cupones())->EliminarPromocion(714);
$esperado = [
    ["DELETE FROM cupones_emitidos WHERE ID_Promocion = ?", 714],
    ["DELETE FROM promociones WHERE ID_Promocion = ?", 714],
];

if ($resultado !== true || $testConnection->executed !== $esperado) {
    fwrite(STDERR, "FAIL: se esperaba borrar los cupones emitidos y la promoción con ID 714.\n");
    exit(1);
}

if (!$testConnection->transactionStarted || !$testConnection->committed || $testConnection->rolledBack) {
    fwrite(STDERR, "FAIL: ambas eliminaciones deben confirmarse en una sola transacción.\n");
    exit(1);
}

echo "PASS: elimina cupones emitidos y promoción de forma atómica.\n";