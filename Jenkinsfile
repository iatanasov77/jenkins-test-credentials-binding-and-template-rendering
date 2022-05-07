/*
 * https://www.lambdatest.com/blog/use-jenkins-shared-libraries-in-a-jenkins-pipeline/
 * https://www.jenkins.io/doc/book/pipeline/shared-libraries/
 * ---------------------------------------------------------------------
 * See File: /var/lib/jenkins/org.jenkinsci.plugins.workflow.libs.GlobalLibraries.xml
 */
@Library( 'VankosoftGroovyLib' ) _

/*
 * TEST CREDENTIALS BINDING
 * ------------------------
 * Using this Plugin:   https://www.jenkins.io/doc/pipeline/steps/credentials-binding/
 * Using this tutorial: https://plugins.jenkins.io/credentials-binding/
 *
 * TEST TEMPLATE RENDERING
 * -----------------------
 * Using this tutorial: https://gist.github.com/gavvvr/651e6915db3c5b37068cf2dadcae34bc
 * =====================================================================================
 * Find Rendered File in: /var/lib/jenkins/jobs/TEST CREDENTIALS BINDING AND TEMPLATE RENDERING/workspace
 */
node ( label: 'php-host' ) {
    String[] environments = ["production", "staging"]
    def BUILD_ENVIRONMENT
    def BRANCH_NAME
    def DO_BACKUP
    
    final GIT_REPO_URL  = 'https://github.com/iatanasov77/jenkins-test-credentials-binding-and-template-rendering.git'
    final APP_FTP_URL   = 'ftp://164.138.221.242/web/project/'
    
    def GIT_REPO_WITH_CRED;
    def APP_MYSQL_USER;
    def APP_MYSQL_PASSWORD;
    def APP_MYSQL_DATABASE;
    
    def CONFIG_TEMPLATE;
    def APP_FTP_USER;
    def APP_FTP_PASSWORD;
    def APP_DATABASE_URL;
    
    stage( 'Configure Environement and Bind Credentials' ) {
    
        // Bind Git Credentials
        withCredentials([usernamePassword(credentialsId: 'github-iatanasov77', usernameVariable: 'USERNAME', passwordVariable: 'PASSWORD')]) {
            GIT_REPO_WITH_CRED = "https://$USERNAME:$PASSWORD@github.com/iatanasov77/jenkins-test-credentials-binding-and-template-rendering.git"
            
        }

        BUILD_ENVIRONMENT   = input message: 'Select Environment', ok: 'Proceed!',
                                parameters: [choice(name: 'Select Environment', choices: "${environments.join('\n')}", description: 'What environment to build?')]
        
        switch( BUILD_ENVIRONMENT ) {            
            case 'production':
                DO_BACKUP   = false
                
                def tags    = vankosoftJob.getGitTags( GIT_REPO_WITH_CRED )
                
                BRANCH_NAME = input message: 'Select Tag', ok: 'Proceed!',
                                    parameters: [choice(name: 'Select a Tag', choices: "${tags.join('\n')}", description: 'What tag to deploy?')]
                                
                break;
                
            default:
                DO_BACKUP   = false
                
                def branches    = vankosoftJob.getGitBranches( GIT_REPO_WITH_CRED )
                
                BRANCH_NAME     = input message: 'Select Branch', ok: 'Proceed!',
                                        parameters: [choice(name: 'Select a Branch', choices: "${branches.join('\n')}", description: 'What branch to deploy?')]
        }
        
        // Bind MySql Credentials
        withCredentials([usernamePassword(credentialsId: 'mysql-jenkins-test', usernameVariable: 'USERNAME', passwordVariable: 'PASSWORD')]) {            
            APP_MYSQL_USER      = "$USERNAME"
            APP_MYSQL_PASSWORD  = "$PASSWORD"
            if ( BUILD_ENVIRONMENT == 'production' ) {
                APP_MYSQL_DATABASE  = "JenkinsTest_Production"
            } else {
                APP_MYSQL_DATABASE  = "JenkinsTest_Staging"
            }
            APP_DATABASE_URL="mysql://$APP_MYSQL_USER:$APP_MYSQL_PASSWORD@127.0.0.1:3306/$APP_MYSQL_DATABASE"
        }
        
        // Bind FTP Credentials
        withCredentials([usernamePassword(credentialsId: 'ftp-jenkins-test', usernameVariable: 'USERNAME', passwordVariable: 'PASSWORD')]) {
            //Using in Template Rendering
            APP_FTP_USER        = "$USERNAME"
            APP_FTP_PASSWORD    = "$PASSWORD"
        }
    }
    
    stage( 'Source Checkout' ) {
        if ( BUILD_ENVIRONMENT == 'production' ) {
            checkout([$class: 'GitSCM', 
                branches: [[name: "refs/tags/${BRANCH_NAME}"]], 
                userRemoteConfigs: [[
                    credentialsId: 'gitlab-iatanasov77', 
                    refspec: '+refs/tags/*:refs/remotes/origin/tags/*', 
                    url: "${GIT_REPO_URL}"]]
            ])
        } else {
            git(
                url: "${GIT_REPO_URL}",
                credentialsId: 'gitlab-iatanasov77',
                branch: "${BRANCH_NAME}"
            )
        }
    }
    
    stage( 'Phing Build and Genearate Configs' ) {
        if ( BUILD_ENVIRONMENT == 'production' ) {
            sh '''
                export COMPOSER_HOME='/home/vagrant/.composer';
                /usr/local/bin/phing install-production -verbose -debug
            '''
        } else {
            sh '''
                export COMPOSER_HOME='/home/vagrant/.composer';
                /usr/local/bin/phing install-staging -verbose -debug
            '''
        }
        
        CONFIG_TEMPLATE = readFile( "ftp_deploy.ini.${BUILD_ENVIRONMENT}" )
        writeFile file: 'ftp_deploy.ini',
                text: vankosoftJob.renderTemplate( CONFIG_TEMPLATE, ['url': APP_FTP_URL, 'user': APP_FTP_USER, 'password': APP_FTP_PASSWORD] )
                
        CONFIG_TEMPLATE = readFile( ".env.${BUILD_ENVIRONMENT}" )
        writeFile file: '.env',
                text: vankosoftJob.renderTemplate( CONFIG_TEMPLATE, ['database_url': APP_DATABASE_URL] )
    }
    
    stage( 'Ftp Deploy' ) {
        sh """
            /usr/bin/php /usr/local/bin/ftpdeploy ftp_deploy.ini
            
            returnCode=\$?   # Capture return code
            exit \$returnCode
        """
        
        // Clear Config
        /*
        sh """
            rm -f ftp_deploy.ini
            rm -f .env
        """
        */
    }

}