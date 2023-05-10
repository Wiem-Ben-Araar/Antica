/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package Service;

import Models.Artiste;
import java.sql.Connection;
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
public class ArtisteService {
    Connection  conn = MaConnexion.getInstance().getCnx();
//     BlogService bs = new BlogService();
    private Statement ste;
    private PreparedStatement pst;
    private ResultSet rs;
    
    public List<Artiste> AfficherArtiste() {
       List<Artiste> list = new ArrayList<>();
        String req = " SELECT * FROM artiste";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                list.add(new Artiste(rs.getInt("id_artiste"), rs.getString("nom")));
            }
        } catch (SQLException ex) {
            Logger.getLogger(ExpertiseService.class.getName()).log(Level.SEVERE, null, ex);
        } 
         System.out.println(list);
        return list;    } 
    public Artiste GetArtiste(int id_artiste) {
       Artiste artiste = new Artiste();
        String req = " SELECT * FROM artiste where id_artiste ="+id_artiste;
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                artiste= new Artiste(rs.getInt("id_artiste"), rs.getString("nom"));
            }
        } catch (SQLException ex) {
            Logger.getLogger(ExpertiseService.class.getName()).log(Level.SEVERE, null, ex);
        } 
         System.out.println(artiste);
        return artiste;    } 
}
