/*
 * https://www.lambdatest.com/blog/use-jenkins-shared-libraries-in-a-jenkins-pipeline/
 * https://www.jenkins.io/doc/book/pipeline/shared-libraries/
 * ---------------------------------------------------------------------
 * See File: /var/lib/jenkins/org.jenkinsci.plugins.workflow.libs.GlobalLibraries.xml
 */
@Library( 'VankosoftGroovyLib' ) _

node {

    // Test Credentials Binding
    def GIT_REPO_WITH_CRED;

    
    // Test Template Rendering

    // Just an example, for Rails config better use embedded ERB
    final DATABASE_YAML_TEMPLATE = '''
    test:
      adapter: oracle_enhanced
      database: app_db
      username: ${user}
      password: ${password}
    '''
    final CFG_PATH = 'config/database.yml'
    def APP_DB_USR;
    def APP_DB_PSW;
    

    stage( 'Test Credentials Binding' ) {

        /*
         * Using this Plugin:   https://www.jenkins.io/doc/pipeline/steps/credentials-binding/
         * Using this tutorial: https://plugins.jenkins.io/credentials-binding/
         */
        withCredentials([usernamePassword(credentialsId: 'gitlab-iatanasov77', usernameVariable: 'USERNAME', passwordVariable: 'PASSWORD')]) {
            GIT_REPO_WITH_CRED = "https://$USERNAME:$PASSWORD@gitlab.com/iatanasov77/vankosoft.org.git"
            
            //Using in Template Rendering
            APP_DB_USR  = "$USERNAME"
            APP_DB_PSW  = "$PASSWORD"
        }
        
        echo GIT_REPO_WITH_CRED
    }
    
    stage( 'Test Template Rendering' ) {
        
        // Using this tutorial: https://gist.github.com/gavvvr/651e6915db3c5b37068cf2dadcae34bc
        /////////////////////////////////////////////////////////////////////////////////////////////// 
        //
        // Find Rendered File in: /var/lib/jenkins/jobs/TEST CREDENTIALS BINDING AND TEMPLATE RENDERING/workspace
        //
        writeFile file: CFG_PATH,
                text: vankosoftJob.renderTemplate(DATABASE_YAML_TEMPLATE, ['user': APP_DB_USR, 'password': APP_DB_PSW])
    }
    
    stage( 'Clear Rendered Template ' ) {
        // Uncomment Bellow To Clearing
        //////////////////////////
        
        // sh "rm -f ${CFG_PATH}"
    }

}