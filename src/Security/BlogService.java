/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
package Service;

import Interfaces.BlogInterface;
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
    public class BlogService implements BlogInterface{
  Connection  conn = MaConnexion.getInstance().getCnx();
    private Statement ste;
    private PreparedStatement pst;
    private ResultSet rs;
    private Object java;
    private ArtisteService artisteService = new ArtisteService();

    @Override
    public void AjouterBlog(Blog object) {
 try {
            String req = "INSERT INTO blog(titre, artiste, contenu,etiquette) VALUES ('" + object.getTitre() + "','" + object.getArtiste().getId_artiste() + "','" + object.getContenu() + "','" + object.getEtiquette() + "')";
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("Blog Added successfully!");
        } catch (SQLException ex) {
            System.out.println(ex.getMessage());
        }   
    }

    @Override
    public void ModifierBlog(Blog object,int id) {
    String req = "UPDATE blog SET titre ='" + object.getTitre() + "',artiste ='" + object.getArtiste().getId_artiste() + "',contenu='" + object.getContenu() + "',etiquette = '" + object.getEtiquette()+ "' WHERE id_blog = '" + id + "'";    
     try {
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("modification avec succés");
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }    }

    @Override
    public void SupprimerBlog(int id) {
 String req = "DELETE FROM blog WHERE id_blog='" + id + "'";
        try {
            ste = conn.createStatement();
            ste.executeUpdate(req);
            System.out.println("blog supprimer avec succés");
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }     }

    @Override
    public List<Blog> RechercherBlog(String titre) {
List<Blog> list = new ArrayList<>();
        String req = " SELECT * FROM blog WHERE titre=" + titre+ "'";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                list.add(new Blog(rs.getInt("id_blog"), rs.getString("titre"),artisteService.GetArtiste(rs.getInt("artiste")) , rs.getDate("date_publication"), rs.getString("contenu"),rs.getString("etiquette")));
            }
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return list;    }

    @Override
    public List<Blog> AfficherBlogs() {
List<Blog> list = new ArrayList<>();
        String req = " SELECT * FROM blog";
        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                list.add(new Blog(rs.getInt("id_blog"), rs.getString("titre"), artisteService.GetArtiste(rs.getInt("artiste")), rs.getDate("date_publication"), rs.getString("contenu"),rs.getString("etiquette")));
            }
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        } 
         System.out.println(list);
        return list;    }

    @Override
    public Blog getBlogById(int id_blog) {
  Blog b = new Blog();
        String req = "select * from blog WHERE id_blog='" + id_blog + "'";  
     try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            rs.next();
            b = new Blog(rs.getInt("id_blog"), rs.getString("titre"),artisteService.GetArtiste(rs.getInt("artiste")), rs.getDate("date_publication"), rs.getString("contenu"), rs.getString("etiquette"));
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return b;    }

    @Override
    public List<Blog> TrieBlog(String object) {
List<Blog> list = new ArrayList<>();
        String req;
        switch (object) {
            case "titre":
                req = " SELECT * FROM blog  OrderBy blog";
                break;
            case "artiste":
                req = " SELECT * FROM blog  OrderBy artiste";
                break;
            case "date_publication":
                req = " SELECT * FROM blog  OrderBy date_publication";
                break;
            default:
                req = " SELECT * FROM blog  OrderBy id_blog";

        }

        try {
            ste = conn.createStatement();
            rs = ste.executeQuery(req);
            while (rs.next()) {
                list.add(new Blog(rs.getInt("id_blog"), rs.getString("titre"), artisteService.GetArtiste(rs.getInt("artiste")), rs.getDate("date_publication"), rs.getString("contenu"), rs.getString("etiquette")));
            }
        } catch (SQLException ex) {
            Logger.getLogger(BlogService.class.getName()).log(Level.SEVERE, null, ex);
        }
        return list;
    }    }

    
    
    