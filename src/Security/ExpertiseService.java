/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package Service;

import Interfaces.ExpertiseInterfaces;
import Models.Expertise;
import java.sql.Connection;
import java.sql.Date;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import utilities.MaConnexion;

/**
 *
 * @author dridi
 */
public class ExpertiseService implements ExpertiseInterfaces {
     Connection  conn = MaConnexion.getInstance().getCnx();
     BlogService bs = new BlogService();
    private Statement ste;
    private PreparedStatement pst;
    private ResultSet rs;
    private Object java;
    ArtisteService artisteService = new ArtisteService();
    ProduitService produitService = new ProduitService();

    @Override
    public void AjouterExpertise(Expertise E) {
        try {
             String req = "INSERT INTO `expertise`(`id_expertise`,`id_produit`,`id_artiste`,`prix_estime`,`condition_produit`,`date_expertise`) VALUES ('" + E.getId_expert() + "','" + E.getId_produit().getId_produit() + "','" + E.getId_artiste().getId_artiste() + "','" + E.getPrix_estimé() + "','" + E.getCondition_produit() + "','" + E.getDate_expertise() + "')";
             ste = conn.createStatement();
             ste.executeUpdate(req);
              System.out.println("expertise Added successfully!");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }
    }
   
    public void ModifierExpertise(Expertise object,int id_expertise) {
         String req = "UPDATE expertise SET prix_estime= "+ object.getPrix_estimé()+ " , condition_produit = '"+object.getCondition_produit()+"'  WHERE id_expertise="+id_expertise;
     try {
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("modification avec succés");
        } catch (SQLException ex) {
            Logger.getLogger(ExpertiseService.class.getName()).log(Level.SEVERE, null, ex);
        }    }
    @Override
    public void SupprimerExpertise(int id_expert) {
         String req = "DELETE FROM expertise WHERE id_expertise ='" + id_expert+ "'";
        try {
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("Prix_estimé supprimer avec succés");
        } catch (SQLException ex) {
            Logger.getLogger(ExpertiseService.class.getName()).log(Level.SEVERE, null, ex);
        }     }

    public List<Expertise> RechercherExpertise(int id_produit) {
       List<Expertise> list = new ArrayList<>();
        String req = " SELECT * FROM blog WHERE id_produit=" + id_produit+ "'";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                 list.add(new Expertise(produitService.GetProduit(id_produit)));
            }
        } catch (SQLException ex) {
            Logger.getLogger(ExpertiseService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return list;    }

    @Override
    public List<Expertise> AfficherExpertise() {
       List<Expertise> list = new ArrayList<>();
        String req = " SELECT * FROM expertise";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                list.add(new Expertise(rs.getInt("id_expertise"),produitService.GetProduit(rs.getInt("id_produit")),artisteService.GetArtiste(rs.getInt("id_artiste")) ,rs.getFloat("prix_estime"),rs.getString("condition_produit"), rs.getDate("date_expertise")));
            }
        } catch (SQLException ex) {
            Logger.getLogger(ExpertiseService.class.getName()).log(Level.SEVERE, null, ex);
        } 
         System.out.println(list);
        return list;    }    

    @Override
    public List<Expertise> RechercherExpertise(String object) {
        throw new UnsupportedOperationException("Not supported yet."); //To change body of generated methods, choose Tools | Templates.
    }

   



   
}
