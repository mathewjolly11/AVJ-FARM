<?php
if (!defined('ABSPATH')) {
    exit;
}

function debug_log($message = null) {
    static $logs = [];

    if ($message) {
        $logs[] = $message;
        error_log($message);
    }

    return $logs;
}

/**
 * Helper function to determine the destination path for the binary.
 * Returns the path to ~/bin/ssl-manager.
 */
function asm_get_binary_dest() {
    if ( function_exists('posix_getuid') && function_exists('posix_getpwuid') ) {
        $userInfo = posix_getpwuid(posix_getuid());
        $homeDir  = isset($userInfo['dir']) ? rtrim($userInfo['dir'], '/') : null;
    } else {
        $homeDir = getenv('HOME');
    }
    if ( ! $homeDir ) {
        $homeDir = '/tmp';
    }
    return $homeDir . '/bin/ssl-manager';
}

/**
 * Check if the manager binary exists and is executable.
 */
function managerInstalled($path) {
    return file_exists( $path ) && is_executable( $path );
}

function managerExec($path, $command, $silenceErr) {
    $cmd = $path . (DEBUG ? ' --debug ' : ' ') . $command . ($silenceErr ? ' 2>/dev/null' : ' 2>&1');
    debug_log('Running command: ' . $cmd);
    return shell_exec(escapeshellcmd($cmd));
}

function getManagerInfo($path) {
    debug_log("Fetching manager info.");
    $infoOutput = managerInstalled($path) ? managerExec($path, 'info', true) : "Manager is not installed";
    debug_log("Manager info: " . $infoOutput);
    return $infoOutput;
}

function isAlreadyRegistered($infoOutput) {
    return isset($infoOutput) && strpos($infoOutput, "Manager is ready") !== false;
}

/**
 * Execute the registration command using the provided token.
 */
function register_manager($path, $token, $panelCredential) {
    $escapedToken = escapeshellarg($token);
    $escapedUrl   = escapeshellarg(API_BASE_URL . '/');

    $cmd = "register -t $escapedToken -u $escapedUrl";

    if (!empty($panelCredential)) {
        $escapedPassword = escapeshellarg($panelCredential);
        $cmd .= " -p $escapedPassword";
    }

    return managerExec($path, $cmd, false);
}

/**
 * Older versions of php do not support the ?? operator.
 */
function coalesce($value, $fallback) {
    return isset($value) ? $value : $fallback;
}

/**
 * Run the manager report command and parse JSON output.
 * Supports output as a single JSON array or newline-delimited JSON.
 */
function getManagerReport($path) {
    if (!managerInstalled($path)) {
        return false;
    }
    $rawReport = managerExec($path, 'report', true);
    $rawReport = trim($rawReport);
    if (empty($rawReport)) {
        return [];
    }
    // If the report starts with a '[' then assume it's a JSON array.
    if (strpos($rawReport, '[') === 0) {
        $decoded = json_decode($rawReport, true);
        return is_array($decoded) ? $decoded : [];
    } else {
        $lines = explode("\n", $rawReport);
        $reportItems = [];
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line !== '') {
                $decoded = json_decode($line, true);
                if ($decoded) {
                    // If this line decodes to an array of items, add them individually.
                    if (is_array($decoded) && isset($decoded[0])) {
                        foreach ($decoded as $entry) {
                            $reportItems[] = $entry;
                        }
                    } else {
                        $reportItems[] = $decoded;
                    }
                }
            }
        }
        return $reportItems;
    }
}
