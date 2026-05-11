<?php

namespace App\Models;

use CodeIgniter\Model;

class UserExportModel extends Model
{
    /**
     * Générer un PDF avec tableaux structurés pour les suggérations et abonnements
     */
    public function generatePdf(array $user, array $suggestions, array $subscriptions): string
    {
        $escape = function($s) {
            return str_replace(['\\','(',')'], ['\\\\','\\(','\\)'], $s);
        };

        $objects = [];
        $objects[] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";

        $content = '';
        $y = 800;
        $lineHeight = 18;
        $marginLeft = 30;
        $pageWidth = 595;
        $marginRight = 30;

        // Titre
        $content .= "BT /F1 20 Tf " . $marginLeft . " " . $y . " Td (NutriPlan) Tj ET\n";
        $y -= $lineHeight * 1.3;

        // Utilisateur
        $content .= "BT /F1 13 Tf " . $marginLeft . " " . $y . " Td (Utilisateur : " . $escape(substr($user['nom'] ?? 'Utilisateur', 0, 50)) . ") Tj ET\n";
        $y -= $lineHeight * 1.8;

        // Section Suggestions
        $content .= "BT /F1 15 Tf " . $marginLeft . " " . $y . " Td (Suggestions de regimes :) Tj ET\n";
        $y -= $lineHeight * 1.5;

        if (!empty($suggestions)) {
            // Colonnes optimisées pour utiliser toute la largeur (sans colonne Statut)
            $colWidths = [105, 85, 105, 60, 90, 60];
            $headers = ['Regime', 'Sport', 'Prix', 'Var', 'Composition', 'Jours'];
            $x = $marginLeft;

            // Ligne d'en-têtes
            foreach ($headers as $i => $h) {
                $content .= $this->drawTableCell($x, $y, $colWidths[$i], $lineHeight, $escape(substr($h, 0, 15)), true);
                $x += $colWidths[$i];
            }
            $y -= $lineHeight;

            // Lignes de données
            foreach ($suggestions as $s) {
                $x = $marginLeft;
                $cells = [
                    substr($s['diet_nom'] ?? '—', 0, 14),
                    substr($s['sport_libelle'] ?? '—', 0, 10),
                    (isset($s['prix_30']) ? number_format($s['prix_30'],2) . ' €' : '—'),
                    substr(($s['variation_poids_jour'] ?? '—') . 'kg', 0, 7),
                    'V' . ($s['viande_percent'] ?? 0) . '% O' . ($s['volaille_percent'] ?? 0) . '%',
                    (isset($s['jours_estimes']) ? $s['jours_estimes'] : '—')
                ];
                foreach ($cells as $i => $cell) {
                    $content .= $this->drawTableCell($x, $y, $colWidths[$i], $lineHeight, $escape($cell), false);
                    $x += $colWidths[$i];
                }
                $y -= $lineHeight;
            }
        } else {
            $content .= "BT /F1 12 Tf " . $marginLeft . " " . $y . " Td (Aucune suggestion disponible) Tj ET\n";
            $y -= $lineHeight;
        }

        $y -= $lineHeight;

        // Section Abonnements
        $content .= "BT /F1 15 Tf " . $marginLeft . " " . $y . " Td (Abonnements :) Tj ET\n";
        $y -= $lineHeight * 1.5;

        if (!empty($subscriptions)) {
            // Colonnes pour abonnements
            $colWidths = [180, 100, 120, 135];
            $headers = ['Regime', 'Duree', 'Prix', 'Date debut'];
            $x = $marginLeft;

            // Ligne d'en-têtes
            foreach ($headers as $i => $h) {
                $content .= $this->drawTableCell($x, $y, $colWidths[$i], $lineHeight, $escape($h), true);
                $x += $colWidths[$i];
            }
            $y -= $lineHeight;

            // Lignes de données
            foreach ($subscriptions as $s) {
                $x = $marginLeft;
                $cells = [
                    substr($s['diet_nom'] ?? '—', 0, 20),
                    ($s['duree'] ?? '—') . ' jours',
                    (isset($s['prix_paye']) ? number_format($s['prix_paye'],2) . '€' : '—'),
                    substr($s['date_debut'] ?? '—', 0, 12)
                ];
                foreach ($cells as $i => $cell) {
                    $content .= $this->drawTableCell($x, $y, $colWidths[$i], $lineHeight, $escape($cell), false);
                    $x += $colWidths[$i];
                }
                $y -= $lineHeight;
            }
        } else {
            $content .= "BT /F1 12 Tf " . $marginLeft . " " . $y . " Td (Aucun abonnement actif) Tj ET\n";
            $y -= $lineHeight;
        }

        $len = strlen($content);
        $objects[] = "4 0 obj\n<< /Length " . $len . " >>\nstream\n" . $content . "endstream\nendobj\n";
        $objects[] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>\nendobj\n";
        $objects[] = "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $pdf = "%PDF-1.4\n%\xE2\xE3\xCF\xD3\n";
        $offsets = [];
        $pos = strlen($pdf);
        foreach ($objects as $obj) {
            $offsets[] = $pos;
            $pdf .= $obj;
            $pos = strlen($pdf);
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= sprintf("%010d %05d f \n", 0, 65535);
        foreach ($offsets as $off) {
            $pdf .= sprintf("%010d 00000 n \n", $off);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\nstartxref\n" . $xrefPos . "\n%%EOF\n";

        return $pdf;
    }

    /**
     * Dessiner une cellule de tableau en PDF
     */
    private function drawTableCell($x, $y, $w, $h, $text, $isBold = false): string
    {
        $stream = '';
        // Cadre de la cellule
        $stream .= "q 0.5 w " . $x . " " . ($y - $h) . " " . $w . " " . $h . " re S Q\n";
        // Texte centré verticalement avec padding
        $fontSize = $isBold ? 12 : 11;
        $stream .= "BT /F1 " . $fontSize . " Tf " . ($x + 3) . " " . ($y - 12) . " Td (" . $text . ") Tj ET\n";
        return $stream;
    }
}
