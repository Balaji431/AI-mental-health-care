package com.example.mindcare.navigation

import androidx.compose.animation.ExperimentalAnimationApi
import androidx.compose.foundation.layout.fillMaxSize
import androidx.compose.material3.Scaffold
import androidx.compose.runtime.Composable
import androidx.compose.runtime.getValue
import androidx.compose.ui.Modifier
import androidx.navigation.NavDestination
import androidx.navigation.NavGraph.Companion.findStartDestination
import androidx.navigation.NavHostController
import androidx.navigation.compose.NavHost
import androidx.navigation.compose.composable
import androidx.navigation.compose.currentBackStackEntryAsState
import androidx.navigation.compose.rememberNavController
import com.example.mindcare.ui.components.BottomNavBar
import com.example.mindcare.ui.screens.AchievementsScreen
import com.example.mindcare.ui.screens.BreathingExerciseScreen
import com.example.mindcare.ui.screens.CheckInBottomSheet
import com.example.mindcare.ui.screens.CrisisSupportScreen
import com.example.mindcare.ui.screens.GroundingExerciseScreen
import com.example.mindcare.ui.screens.HomeScreen
import com.example.mindcare.ui.screens.InsightsScreen
import com.example.mindcare.ui.screens.JournalScreen
import com.example.mindcare.ui.screens.LoginScreen
import com.example.mindcare.ui.screens.MeditationPlayerScreen
import com.example.mindcare.ui.screens.MoodCalendarScreen
import com.example.mindcare.ui.screens.PhotoEntryScreen
import com.example.mindcare.ui.screens.ProfileScreen
import com.example.mindcare.ui.screens.RegistrationScreen
import com.example.mindcare.ui.screens.SplashScreen
import com.example.mindcare.ui.screens.SupportCircleScreen
import com.example.mindcare.ui.screens.ToolsScreen
import com.example.mindcare.ui.screens.VoiceEntryScreen

sealed class Screen(val route: String) {
    object Splash : Screen("splash")
    object Login : Screen("login")
    object Registration : Screen("registration")

    object Home : Screen("home")
    object Insights : Screen("insights")
    object Journal : Screen("journal")
    object Tools : Screen("tools")
    object Profile : Screen("profile")

    object MoodCalendar : Screen("mood_calendar")
    object VoiceEntry : Screen("voice_entry")
    object PhotoEntry : Screen("photo_entry")
    object Achievements : Screen("achievements")
    object SupportCircle : Screen("support_circle")
    object CrisisSupport : Screen("crisis_support")
    object BreathingExercise : Screen("breathing")
    object MeditationPlayer : Screen("meditation_player")
    object GroundingExercise : Screen("grounding")
}

val BottomNavScreens = listOf(
    Screen.Home,
    Screen.Insights,
    Screen.Journal,
    Screen.Tools,
    Screen.Profile
)

@OptIn(ExperimentalAnimationApi::class)
@Composable
fun MindCareNavGraph() {
    val navController = rememberNavController()
    val backStackEntry by navController.currentBackStackEntryAsState()
    val currentDestination = backStackEntry?.destination

    val showBottomBar = currentDestination?.route in BottomNavScreens.map { it.route }

    Scaffold(
        bottomBar = {
            if (showBottomBar) {
                BottomNavBar(
                    items = BottomNavScreens,
                    currentDestination = currentDestination,
                    onItemSelected = { screen ->
                        navController.navigate(screen.route) {
                            popUpTo(navController.graph.findStartDestination().id) {
                                saveState = true
                            }
                            launchSingleTop = true
                            restoreState = true
                        }
                    }
                )
            }
        }
    ) { innerPadding ->
        NavHost(
            navController = navController,
            startDestination = Screen.Splash.route,
            modifier = Modifier.fillMaxSize()
        ) {
            composable(Screen.Splash.route) {
                SplashScreen(
                    onFinished = {
                        navController.navigate(Screen.Login.route) {
                            popUpTo(Screen.Splash.route) {
                                inclusive = true
                            }
                        }
                    }
                )
            }
            composable(Screen.Login.route) {
                LoginScreen(
                    onLogin = {
                        navController.navigate(Screen.Home.route) {
                            popUpTo(Screen.Login.route) {
                                inclusive = true
                            }
                        }
                    },
                    onSignup = { navController.navigate(Screen.Registration.route) }
                )
            }
            composable(Screen.Registration.route) {
                RegistrationScreen(
                    onRegistered = {
                        navController.navigate(Screen.Home.route) {
                            popUpTo(Screen.Registration.route) {
                                inclusive = true
                            }
                        }
                    },
                    onBackToLogin = { navController.popBackStack() }
                )
            }

            composable(Screen.Home.route) {
                HomeScreen(
                    innerPadding = innerPadding,
                    onOpenCheckIn = {
                        // handled inside HomeScreen via bottom sheet
                    },
                    onOpenBreathing = { navController.navigate(Screen.BreathingExercise.route) },
                    onOpenMeditation = { navController.navigate(Screen.MeditationPlayer.route) },
                    onOpenMoodCalendar = { navController.navigate(Screen.MoodCalendar.route) }
                )
            }
            composable(Screen.Insights.route) {
                InsightsScreen(innerPadding = innerPadding)
            }
            composable(Screen.Journal.route) {
                JournalScreen(
                    innerPadding = innerPadding,
                    onAddTextEntry = { /* bottom sheet is inside JournalScreen */ },
                    onAddVoiceEntry = { navController.navigate(Screen.VoiceEntry.route) },
                    onAddPhotoEntry = { navController.navigate(Screen.PhotoEntry.route) }
                )
            }
            composable(Screen.Tools.route) {
                ToolsScreen(
                    innerPadding = innerPadding,
                    onOpenBreathing = { navController.navigate(Screen.BreathingExercise.route) },
                    onOpenMeditation = { navController.navigate(Screen.MeditationPlayer.route) },
                    onOpenGrounding = { navController.navigate(Screen.GroundingExercise.route) },
                    onOpenCrisisSupport = { navController.navigate(Screen.CrisisSupport.route) }
                )
            }
            composable(Screen.Profile.route) {
                ProfileScreen(
                    innerPadding = innerPadding,
                    onOpenAchievements = { navController.navigate(Screen.Achievements.route) },
                    onOpenSupportCircle = { navController.navigate(Screen.SupportCircle.route) },
                    onOpenCrisisSupport = { navController.navigate(Screen.CrisisSupport.route) },
                    onLogout = {
                        navController.navigate(Screen.Login.route) {
                            popUpTo(Screen.Home.route) {
                                inclusive = true
                            }
                        }
                    }
                )
            }

            composable(Screen.MoodCalendar.route) {
                MoodCalendarScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.VoiceEntry.route) {
                VoiceEntryScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.PhotoEntry.route) {
                PhotoEntryScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.Achievements.route) {
                AchievementsScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.SupportCircle.route) {
                SupportCircleScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.CrisisSupport.route) {
                CrisisSupportScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.BreathingExercise.route) {
                BreathingExerciseScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.MeditationPlayer.route) {
                MeditationPlayerScreen(onBack = { navController.popBackStack() })
            }
            composable(Screen.GroundingExercise.route) {
                GroundingExerciseScreen(onBack = { navController.popBackStack() })
            }
        }
    }
}


