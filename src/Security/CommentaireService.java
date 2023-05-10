/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package Service;

import Interfaces.CommentaireInterface;
import Models.Commentaire;
import java.sql.Connection;
import java.sql.PreparedStatement;
import java.sql.ResultSet;
import java.sql.SQLException;
import java.sql.Statement;
import java.util.ArrayList;
import java.util.List;
import java.util.logging.Level;
import java.util.logging.Logger;
import Models.Blog;
import utilities.MaConnexion;

/**
 *
 * @author dridi
 */
public class CommentaireService implements CommentaireInterface{
     Connection  conn = MaConnexion.getInstance().getCnx();
     BlogService bs = new BlogService();
     UserService userService = new UserService();
    private Statement ste;
    private PreparedStatement pst;
    private ResultSet rs;
    

    @Override
    public void AjouterCommentaire(Commentaire cmnt) {

         try {
             String req = "INSERT INTO `commentaire`(`contenu`,`blog`,`user`) VALUES ('" + cmnt.getContenu() + "',"+cmnt.getBlog().getId_blog()+","+cmnt.getUser().getId_user()+")";
             ste = conn.createStatement();
             ste.executeUpdate(req);
             System.out.println("Commenatire Added successfully!");
         } catch (SQLException ex) {
             Logger.getLogger(CommentaireService.class.getName()).log(Level.SEVERE, null, ex);
         }
       
    }
    @Override
    public void ModifierCommentaire(Commentaire cmnt, int id) {
         String req = "UPDATE commentaire SET contenu ='" + cmnt.getContenu() + "' WHERE id_commentaire = '" + id+ "'";    
    
      try {
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("modification avec succes");
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
    }
    }
    @Override
    public void SupprimerCommentaire(Commentaire object) {}
     public void SupprimerCommentaire1(int i){
        String req = "DELETE FROM commentaire WHERE id_commentaire='" + i+ "'";
         try {
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("supprission avec succes");
        } catch (SQLException ex) {
            Logger.getLogger(CommentaireService.class.getName()).log(Level.SEVERE, null, ex);
        } 
    }

    @Override
    public List<Commentaire> RechercherCommentaire(String object) {
     List<Commentaire> list = new ArrayList<>();
        String req = " SELECT * FROM commentaire WHERE (contenue LIKE '%" + object + "%'";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
           //     list.add(new Commentaire(rs.getInt("id_commentaire"), rs.getString("contenue"),bs.getBlogById(rs.getInt("blog"))));
        
            }
        } catch (SQLException ex) {
            Logger.getLogger(CommentaireService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return list; 
    }

    @Override
    public List<Commentaire> AfficherCommentaire() {
      
    List<Commentaire> list = new ArrayList<>();
        String req = " SELECT * FROM commentaire";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
    list.add(new Commentaire(rs.getInt("id_commentaire"), rs.getString("contenu"),bs.getBlogById(rs.getInt("blog")),userService.getUser(rs.getInt("user"))));
            }
        } catch (SQLException ex) {
            Logger.getLogger(CommentaireService.class.getName()).log(Level.SEVERE, null, ex);
        }
         System.out.println(list);
        return list; 
    }
     @Override
    public List<Commentaire> AfficherCommentaireByBlog(int id) {
      
    List<Commentaire> list = new ArrayList<>();
        String req = " SELECT * FROM commentaire where blog="+id;
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
         list.add(new Commentaire(rs.getInt("id_commentaire"), rs.getString("contenu"),bs.getBlogById(rs.getInt("blog")),userService.getUser(rs.getInt("user"))));
            }
        } catch (SQLException ex) {
            Logger.getLogger(CommentaireService.class.getName()).log(Level.SEVERE, null, ex);
        }
         System.out.println(list);
        return list; 
    }

    @Override
    public Commentaire getCommentaireId(int object) {
         Commentaire c = new Commentaire();
        String req = "select * from commentaire WHERE id_commentaire='" + object + "'";  
     try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            rs.next();
            c = new Commentaire(rs.getInt("id_commentaire"), rs.getString("contenu"),bs.getBlogById(rs.getInt("blog")),userService.getUser(rs.getInt("user")));
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return c;
    }

    @Override
    public List<Commentaire> TrieCommentaire(String object) {
       List<Commentaire> list = new ArrayList<>();
        String req;
        switch (object) {
            case "contenu":
                req = " SELECT * FROM blog  OrderBy contenu";
                break;
            
            default:
                req = " SELECT * FROM blog  OrderBy id_blog";

        }

        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                list.add(new Commentaire(rs.getInt("id_commentaire"), rs.getString("contenu"),bs.getBlogById(rs.getInt("blog")),userService.getUser(rs.getInt("user"))));
            }
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return list;
    }
    //get commentaire par blog

    @Override
    public List<Commentaire> AfficherCommentaireByUser(int id) {
    
    List<Commentaire> list = new ArrayList<>();
        String req = " SELECT * FROM commentaire where user="+id;
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
         list.add(new Commentaire(rs.getInt("id_commentaire"), rs.getString("contenu"),bs.getBlogById(rs.getInt("blog")),userService.getUser(rs.getInt("user"))));
            }
        } catch (SQLException ex) {
            Logger.getLogger(CommentaireService.class.getName()).log(Level.SEVERE, null, ex);
        }
         System.out.println(list);
        return list; 
    }

}
