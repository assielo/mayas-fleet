import pandas as pd
import numpy as np
import os

# Chemin vers le fichier Excel
excel_path = 'Gestion_Recettes_Depenses_Transport_FR.xlsx'
xls = pd.ExcelFile(excel_path)

sql_output_file = 'migration_complete_mayas_fleet.sql'

with open(sql_output_file, 'w', encoding='utf-8') as f:
    f.write("-- Script SQL généré automatiquement pour MAYAS FLEET\n")
    f.write("-- Basé sur l'intégralité du fichier Excel de gestion\n\n")

    # 1. Traitement de la Flotte Automobile
    if 'Flotte Automobile' in xls.sheet_names:
        df_flotte = pd.read_excel(excel_path, sheet_name='Flotte Automobile').dropna(how='all')
        f.write("-- Insertion de la Flotte Automobile\n")
        for _, row in df_flotte.iterrows():
            immat = str(row.get('Immatriculation', 'N/A'))
            marque = str(row.get('Marque', 'N/A'))
            modele = str(row.get('Modèle', 'N/A'))
            chauffeur = str(row.get('Chauffeur Affecté', 'N/A'))
            etat = str(row.get('État du Véhicule', 'actif'))
            if immat != 'nan':
                f.write(f"INSERT INTO vehicles (plate_number, brand, model, status, created_at, updated_at) VALUES ('{immat}', '{marque}', '{modele}', '{etat}', datetime('now'), datetime('now'));\n")
        f.write("\n")

    # 2. Traitement des Recettes Journalières (Octobre à Septembre)
    daily_sheets = [s for s in xls.sheet_names if 'Recettes Journaliere' in s or s in ['Juin ', 'Juillet', 'Aout', 'Septembre']]
    for sheet in daily_sheets:
        df_recettes = pd.read_excel(excel_path, sheet_name=sheet)
        f.write(f"-- Insertion des Recettes Journalières : {sheet}\n")
        # Nettoyage des colonnes pertinentes
        for _, row in df_recettes.iterrows():
            date = row.get('Date')
            vehicule = row.get('Véhicule')
            montant = row.get('Montant (FCFA)')
            carburant = row.get('Carburant (FCFA)')
            
            if pd.notna(date) and pd.notna(montant) and str(montant).replace('.','',1).isdigit():
                f.write(f"INSERT INTO daily_revenues (date, vehicle, amount, fuel_cost, created_at, updated_at) VALUES ('{date}', '{vehicule}', {montant}, {carburant if pd.notna(carburant) else 0}, datetime('now'), datetime('now'));\n")
        f.write("\n")

    # 3. Traitement des Versements Bancaires
    if 'Versement Bancaire' in xls.sheet_names:
        df_versements = pd.read_excel(excel_path, sheet_name='Versement Bancaire').dropna(how='all')
        f.write("-- Insertion des Versements Bancaires\n")
        for _, row in df_versements.iterrows():
            date_v = row.get('Date du verssement')
            montant = row.get('Montant (FCFA)')
            desc = str(row.get('Description', ''))
            if pd.notna(date_v) and pd.notna(montant) and str(montant).replace('.','',1).isdigit():
                f.write(f"INSERT INTO bank_deposits (deposit_date, amount, description, created_at, updated_at) VALUES ('{date_v}', {montant}, '{desc.replace("'", "''")}', datetime('now'), datetime('now'));\n")
        f.write("\n")

    # 4. Traitement du Suivi des Assurances
    if 'Suivi Assurance' in xls.sheet_names:
        df_assurance = pd.read_excel(excel_path, sheet_name='Suivi Assurance').dropna(how='all')
        f.write("-- Insertion du Suivi des Assurances\n")
        for _, row in df_assurance.iterrows():
            compagnie = str(row.get('Compagnie Assurance', ''))
            modele = str(row.get('Modèle', ''))
            debut = row.get('Date Début')
            exp = row.get('Date Expiration')
            prime = row.get('Prime Mensuelle (FCFA)')
            statut = str(row.get('Statut Paiement', 'Payé'))
            
            if pd.notna(prime) and str(prime).replace('.','',1).isdigit():
                f.write(f"INSERT INTO insurances (company, model, start_date, expiry_date, monthly_premium, status, created_at, updated_at) VALUES ('{compagnie}', '{modele}', '{debut}', '{exp}', {prime}, '{statut}', datetime('now'), datetime('now'));\n")
        f.write("\n")

print(f"Script SQL généré avec succès : {sql_output_file}")