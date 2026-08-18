<?php
/**
 * Git Configuration and Execution Helpers
 */

require_once __DIR__ . '/database.php';

function getGitConfig(PDO $pdo): array {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM clinic_settings WHERE setting_key IN ('git_path', 'git_repo_dir', 'update_branch', 'git_remote_url')");
    $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

    $defaultRepoDir = realpath(__DIR__ . '/..') ?: dirname(__DIR__);

    return [
        'git_path'       => $rows['git_path'] ?? 'git',
        'git_repo_dir'   => $rows['git_repo_dir'] ?? $defaultRepoDir,
        'update_branch'  => $rows['update_branch'] ?? 'main',
        'git_remote_url' => $rows['git_remote_url'] ?? ''
    ];
}

function isGitRepoRobust(string $gitPath, string $gitRepoDir): bool {
    if (!$gitRepoDir || !is_dir($gitRepoDir)) {
        return false;
    }
    $gitCmd = escapeshellarg($gitPath ?: 'git') . " -C " . escapeshellarg($gitRepoDir) . " -c safe.directory=* rev-parse --is-inside-work-tree 2>&1";
    $output = shell_exec($gitCmd);
    return ($output !== null && trim($output) === 'true');
}

function runGitCmd(string $gitPath, string $gitRepoDir, string $subCmd): string {
    $gitCmd = escapeshellarg($gitPath ?: 'git') . " -C " . escapeshellarg($gitRepoDir) . " -c safe.directory=* " . $subCmd . " 2>&1";
    return (string)shell_exec($gitCmd);
}
