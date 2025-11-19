<?php
/**
 * Configuration et connexion à la base de données
 */

/**
 * Obtenir la connexion PDO à la base de données
 * @return PDO Instance de connexion PDO
 * @throws PDOException Si la connexion échoue
 */
function getDbConnection(): PDO {
    // Récupération des variables d'environnement (obligatoires)
    $host = getenv('DB_HOST');
    $port = getenv('DB_PORT');
    $dbname = getenv('DB_NAME');
    $user = getenv('DB_USER');
    $password = getenv('DB_PASSWORD');
    
    // Vérification que toutes les variables sont définies
    if (!$host || !$port || !$dbname || !$user || $password === false) {
        throw new PDOException("Variables d'environnement de base de données manquantes. Vérifiez votre fichier .env");
    }
    
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        
        $pdo = new PDO($dsn, $user, $password, $options);
        return $pdo;
    } catch (PDOException $e) {
        error_log("Erreur de connexion à la base de données : " . $e->getMessage());
        throw new PDOException("Impossible de se connecter à la base de données.");
    }
}

/**
 * Récupérer l'état actuel de la caisse depuis la base de données
 * @return array|null État de la caisse ou null si erreur
 */
function getCaisseState(): ?array {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT * FROM caisse_state ORDER BY id DESC LIMIT 1");
        $state = $stmt->fetch();
        
        if ($state) {
            // Retirer les champs non nécessaires
            unset($state['id'], $state['updated_at'], $state['updated_by']);
        }
        
        return $state ?: null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'état de la caisse : " . $e->getMessage());
        return null;
    }
}

/**
 * Sauvegarder l'état de la caisse dans la base de données
 * @param array $caisse État de la caisse à sauvegarder
 * @param int|null $userId ID de l'utilisateur effectuant la modification
 * @return bool True si succès, false sinon
 */
function saveCaisseState(array $caisse, ?int $userId = null): bool {
    try {
        $pdo = getDbConnection();
        
        $sql = "INSERT INTO caisse_state (
            billet_500, billet_200, billet_100, billet_50, billet_20, billet_10, billet_5,
            piece_2, piece_1, piece_050, piece_020, piece_010, piece_005, piece_002, piece_001,
            updated_by
        ) VALUES (
            :billet_500, :billet_200, :billet_100, :billet_50, :billet_20, :billet_10, :billet_5,
            :piece_2, :piece_1, :piece_050, :piece_020, :piece_010, :piece_005, :piece_002, :piece_001,
            :updated_by
        )";
        
        $stmt = $pdo->prepare($sql);
        
        $params = $caisse;
        $params['updated_by'] = $userId;
        
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Erreur lors de la sauvegarde de l'état de la caisse : " . $e->getMessage());
        return false;
    }
}

/**
 * Enregistrer une transaction dans l'historique
 * @param array $transactionData Données de la transaction
 * @return bool True si succès, false sinon
 */
function saveTransactionHistory(array $transactionData): bool {
    try {
        $pdo = getDbConnection();
        
        $sql = "INSERT INTO caisse_history (
            montant_du, montant_donne, montant_rendu, algorithme, valeur_preferee,
            monnaie_rendue, caisse_avant, caisse_apres, user_id
        ) VALUES (
            :montant_du, :montant_donne, :montant_rendu, :algorithme, :valeur_preferee,
            :monnaie_rendue, :caisse_avant, :caisse_apres, :user_id
        )";
        
        $stmt = $pdo->prepare($sql);
        
        // Conversion des arrays en JSON
        $params = [
            'montant_du' => $transactionData['montant_du'],
            'montant_donne' => $transactionData['montant_donne'],
            'montant_rendu' => $transactionData['montant_rendu'],
            'algorithme' => $transactionData['algorithme'] ?? 'glouton',
            'valeur_preferee' => $transactionData['valeur_preferee'] ?? null,
            'monnaie_rendue' => json_encode($transactionData['monnaie_rendue']),
            'caisse_avant' => json_encode($transactionData['caisse_avant']),
            'caisse_apres' => json_encode($transactionData['caisse_apres']),
            'user_id' => $transactionData['user_id'] ?? null
        ];
        
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Erreur lors de l'enregistrement de la transaction : " . $e->getMessage());
        return false;
    }
}

/**
 * Récupérer l'historique des transactions
 * @param int $limit Nombre de transactions à récupérer (0 = toutes)
 * @param int $offset Décalage pour la pagination
 * @param int|null $userId ID de l'utilisateur (null = tous)
 * @return array|null Liste des transactions ou null si erreur
 */
function getTransactionHistory(int $limit = 0, int $offset = 0, ?int $userId = null): ?array {
    try {
        $pdo = getDbConnection();
        
        $sql = "SELECT * FROM caisse_history";
        
        // Filtrer par utilisateur si spécifié
        if ($userId !== null) {
            $sql .= " WHERE user_id = :user_id";
        }
        
        $sql .= " ORDER BY transaction_date DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT :limit OFFSET :offset";
        }
        
        $stmt = $pdo->prepare($sql);
        
        if ($userId !== null) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }
        
        if ($limit > 0) {
            $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $transactions = $stmt->fetchAll();
        
        // Décoder les JSON
        foreach ($transactions as &$transaction) {
            $transaction['monnaie_rendue'] = json_decode($transaction['monnaie_rendue'], true);
            $transaction['caisse_avant'] = json_decode($transaction['caisse_avant'], true);
            $transaction['caisse_apres'] = json_decode($transaction['caisse_apres'], true);
        }
        
        return $transactions;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'historique : " . $e->getMessage());
        return null;
    }
}

/**
 * Compter le nombre total de transactions
 * @param int|null $userId ID de l'utilisateur (null = tous)
 * @return int Nombre de transactions
 */
function countTransactions(?int $userId = null): int {
    try {
        $pdo = getDbConnection();
        
        $sql = "SELECT COUNT(*) as total FROM caisse_history";
        
        if ($userId !== null) {
            $sql .= " WHERE user_id = :user_id";
        }
        
        $stmt = $pdo->prepare($sql);
        
        if ($userId !== null) {
            $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        }
        
        $stmt->execute();
        $result = $stmt->fetch();
        return $result ? (int)$result['total'] : 0;
    } catch (PDOException $e) {
        error_log("Erreur lors du comptage des transactions : " . $e->getMessage());
        return 0;
    }
}

/**
 * Récupérer tous les utilisateurs
 * @return array|null Liste des utilisateurs ou null si erreur
 */
function getAllUsers(): ?array {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->query("SELECT id, email, role, created_at FROM users ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération des utilisateurs : " . $e->getMessage());
        return null;
    }
}

/**
 * Récupérer un utilisateur par ID
 * @param int $userId ID de l'utilisateur
 * @return array|null Informations de l'utilisateur ou null
 */
function getUserById(int $userId): ?array {
    try {
        $pdo = getDbConnection();
        $stmt = $pdo->prepare("SELECT id, email, role, created_at FROM users WHERE id = :id");
        $stmt->execute(['id' => $userId]);
        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        error_log("Erreur lors de la récupération de l'utilisateur : " . $e->getMessage());
        return null;
    }
}

