pipeline {
    agent any

    environment {
        DEPLOY_DIR = 'C:\\inetpub\\wwwroot\\BusManagerApp'
    }

    stages {

        stage('Checkout Code') {
            steps {
                git branch: 'main', url: 'https://github.com/MV1306/BusManagementApp.git'
            }
        }

        stage('Clean Old Files') {
            steps {
                powershell '''
                Write-Output "Cleaning old deployment directory..."
                Remove-Item -Recurse -Force "$env:DEPLOY_DIR\\*" -ErrorAction SilentlyContinue
                '''
            }
        }

        stage('Copy Files to IIS') {
            steps {
                powershell '''
                Write-Output "Copying new files to deployment directory..."
                Copy-Item -Path "$env:WORKSPACE\\*" -Destination "$env:DEPLOY_DIR" -Recurse -Force
                '''
            }
        }

        stage('Restart IIS App Pool') {
            steps {
                powershell '''
                Write-Output "Restarting IIS App Pool: BusManagerApp"
                Import-Module WebAdministration
                Restart-WebAppPool -Name "BusManagerApp"
                '''
            }
        }

    }

    post {
        success {
            echo 'Deployment to IIS completed successfully!'
        }
        failure {
            echo 'Deployment failed. Check logs.'
        }
    }
}
